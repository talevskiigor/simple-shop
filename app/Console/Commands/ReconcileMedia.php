<?php
namespace App\Console\Commands;

use App\Models\{Media, Product, Page};
use App\Services\{MediaReferences, MediaFiles, ContentHtml};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, File};

class ReconcileMedia extends Command
{
    protected $signature = 'media:reconcile {--apply : Reconcile references and archive/remove verified unused or duplicate originals} {--backup= : Private recovery directory required with --apply}';
    protected $description = 'Audit restored media; dry run by default, with recoverable staging cleanup';

    public function handle(): int
    {
        $lock = fopen(storage_path('framework/cache/media-reconcile.lock'), 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) { $this->error('Another media reconciliation is running.'); return self::FAILURE; }
        try { return $this->reconcile(); } finally { flock($lock, LOCK_UN); fclose($lock); }
    }

    private function reconcile(): int
    {
        $root = realpath(config('media.root'));
        if (!$root || !is_dir($root.'/images')) { $this->error('Media root is unavailable.'); return self::FAILURE; }
        $references = app(MediaReferences::class)->inventory();
        $files = [];
        foreach (File::allFiles($root.'/images') as $file) {
            if ($file->isLink() || !str_starts_with($file->getRealPath(), $root.'/')) continue;
            $path = 'images/'.$file->getRelativePathname();
            $files[$path] = ['path' => $path, 'sha256' => hash_file('sha256', $file), 'bytes' => $file->getSize(), 'used' => isset($references[$path]), 'mime' => mime_content_type($file->getPathname())];
        }
        $groups = collect($files)->groupBy('sha256');
        $aliases = []; $remove = [];
        foreach ($groups as $group) {
            $used = $group->where('used', true)->sortBy('path');
            $canonical = $used->first()['path'] ?? null;
            foreach ($group as $file) {
                if (!$canonical) $remove[$file['path']] = 'unused';
                elseif ($file['path'] !== $canonical) { $aliases[$file['path']] = $canonical; $remove[$file['path']] = 'duplicate'; }
            }
        }
        $knownAliases = DB::table('media_aliases')->pluck('target', 'path')->all();
        $missing = array_values(array_filter(array_keys($references), fn ($path) => !isset($files[$path]) && !isset($knownAliases[$path])));
        $report = ['files' => count($files), 'remove_unused' => count(array_filter($remove, fn ($reason) => $reason === 'unused')), 'remove_duplicates' => count($aliases), 'remove_bytes' => array_sum(array_map(fn ($path) => $files[$path]['bytes'], array_keys($remove))), 'missing_referenced_paths' => $missing, 'aliases' => $aliases, 'removals' => $remove];
        $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        if (!$this->option('apply')) return self::SUCCESS;
        if (!config('store.sandbox') || !app()->environment(['local', 'staging', 'testing'])) { $this->error('Physical cleanup is restricted to the isolated local/staging rollout.'); return self::FAILURE; }
        if (!app()->environment('testing') && !app()->isDownForMaintenance()) { $this->error('Enable maintenance mode before applying media cleanup.'); return self::FAILURE; }
        $backup = rtrim((string) $this->option('backup'), '/');
        if (!str_starts_with($backup, '/') || file_exists($backup)) { $this->error('Provide a new absolute private --backup directory.'); return self::FAILURE; }
        File::ensureDirectoryExists($backup, 0700, true);
        $backup = realpath($backup);
        if (str_starts_with($backup, realpath(public_path()).'/') || str_starts_with($backup, $root.'/')) { $this->error('Recovery directory must be outside public/media paths.'); return self::FAILURE; }
        chmod($backup, 0700);
        $snapshot = [];
        foreach (['media', 'media_product', 'media_aliases', 'products', 'pages'] as $table) $snapshot[$table] = DB::table($table)->get()->all();
        $this->writePrivate($backup.'/database-records.json', $snapshot);
        $this->writePrivate($backup.'/plan.json', ['report' => $report, 'files' => $files]);
        foreach ($remove as $path => $reason) {
            $target = $backup.'/originals/'.$path;
            File::ensureDirectoryExists(dirname($target), 0700, true);
            if (!copy($root.'/'.$path, $target) || hash_file('sha256', $target) !== $files[$path]['sha256']) throw new \RuntimeException('Recovery copy failed for '.$path);
            chmod($target, 0600);
        }
        DB::transaction(function () use ($aliases, $files, $remove, $root, $knownAliases) {
            foreach ($aliases as $path => $target) {
                DB::table('media_aliases')->updateOrInsert(['path_hash' => hash('sha256', $path)], ['path' => $path, 'target' => $target]);
                DB::table('media_aliases')->where('target', $path)->update(['target' => $target]);
                Product::withTrashed()->where('image', $path)->update(['image' => $target]);
            }
            // One media record per physical path; map duplicate pivots without losing gallery order.
            $canonicalRecords = []; $idMap = [];
            foreach (Media::withTrashed()->orderBy('id')->get() as $media) {
                $path = ltrim($media->path, '/');
                $target = $aliases[$path] ?? $knownAliases[$path] ?? $path;
                if (!$target || (isset($remove[$target]) && $remove[$target] === 'unused')) {
                    $media->delete(); continue;
                }
                if (!isset($canonicalRecords[$target])) {
                    if ($media->trashed()) $media->restore();
                    $media->path = $target; $media->file = basename($target); $media->save();
                    $canonicalRecords[$target] = $media->id;
                } else { $media->forceDelete(); }
                $idMap[$media->id] = $canonicalRecords[$target];
            }
            foreach ($files as $path => $file) {
                if (isset($remove[$path])) continue;
                $info = @getimagesize($root.'/'.$path);
                $data = ['name' => pathinfo($path, PATHINFO_FILENAME), 'path' => $path, 'file' => basename($path), 'type' => str_starts_with($file['mime'], 'video/') ? 'video' : 'image', 'mime' => $file['mime'], 'sha256' => $file['sha256'], 'bytes' => $file['bytes'], 'w' => $info[0] ?? null, 'h' => $info[1] ?? null];
                $media = isset($canonicalRecords[$path]) ? Media::find($canonicalRecords[$path]) : new Media;
                if ($media->exists) unset($data['name']);
                $media->fill($data)->save();
            }
            $pivots = DB::table('media_product')->orderBy('position')->get();
            $products = Product::withTrashed()->pluck('id')->flip();
            $seen = []; $rows = [];
            foreach ($pivots as $pivot) {
                $id = $idMap[$pivot->media_id] ?? null;
                if (!$id || !isset($products[$pivot->product_id])) continue;
                $key = $pivot->product_id.':'.$id;
                if (isset($seen[$key])) continue;
                $seen[$key] = true; $row = (array) $pivot; $row['media_id'] = $id; $rows[] = $row;
            }
            DB::table('media_product')->delete();
            foreach (array_chunk($rows, 200) as $chunk) DB::table('media_product')->insert($chunk);
            foreach ([Product::class => 'description', Page::class => 'body'] as $model => $column) {
                foreach ($model::withTrashed()->get() as $item) {
                    $html = html_entity_decode($item->$column ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $html = preg_replace_callback('~(?:https?://(?:www\.)?forkids\.mk)?/image/catalog/[^"\s<>]+~iu', function ($match) use ($files, $aliases) {
                        $path = 'images/'.basename(rawurldecode($match[0]));
                        return isset($files[$path]) ? app(MediaFiles::class)->url($aliases[$path] ?? $path) : $match[0];
                    }, $html);
                    foreach ($aliases as $from => $to) {
                        $html = str_replace(['/media/'.$from, '/media/'.implode('/', array_map('rawurlencode', explode('/', $from)))], ['/media/'.$to, '/media/'.implode('/', array_map('rawurlencode', explode('/', $to)))], $html);
                    }
                    $item->$column = app(ContentHtml::class)->clean($html);
                    $item->saveQuietly();
                }
            }
        });
        $removed = [];
        foreach ($remove as $path => $reason) {
            // Refuse an unexpected source modification between audit and deletion.
            if (hash_file('sha256', $root.'/'.$path) !== $files[$path]['sha256']) throw new \RuntimeException('Source changed during cleanup: '.$path);
            if (!unlink($root.'/'.$path)) throw new \RuntimeException('Unable to remove archived source: '.$path);
            $removed[] = $path;
        }
        $this->writePrivate($backup.'/completed.json', ['removed' => $removed, 'completed_at' => now()->toIso8601String()]);
        $this->info('Reconciliation completed; private recovery records and original files saved.');
        return self::SUCCESS;
    }

    private function writePrivate(string $file, mixed $value): void {
        if (file_put_contents($file, json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)) === false) throw new \RuntimeException('Cannot write recovery file.');
        chmod($file, 0600);
    }
}
