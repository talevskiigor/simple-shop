<?php
// Read-only operational verification. Never emits secrets or customer records.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (!config('store.sandbox') || !in_array(config('database.connections.'.config('database.default').'.database'), ['local_forkids', 'stg_forkids'], true)) throw new RuntimeException('Isolated copies only.');
$results = [];
foreach (['трицикл'=>'tricikl','количка'=>'kolicka','коцки'=>'kocki'] as $cyrillic => $latin) {
    $a = App\Models\Product::search(Illuminate\Support\Str::ascii($cyrillic))->query(fn ($query) => $query->where('active', true))->get()->modelKeys();
    $b = App\Models\Product::search(Illuminate\Support\Str::ascii($latin))->query(fn ($query) => $query->where('active', true))->get()->modelKeys();
    sort($a); sort($b);
    if ($a !== $b) throw new RuntimeException('Cyrillic search mismatch.');
    $results[$cyrillic.'/'.$latin] = count($a);
}
if (config('database.connections.oc') !== null) throw new RuntimeException('Retired importer connection found.');
$counts = [];
foreach (['products','categories','pages','orders','users','media'] as $table) $counts[$table] = Illuminate\Support\Facades\DB::table($table)->count();
$counts['active_media'] = App\Models\Media::count();
$counts['administrators'] = App\Models\User::where('is_admin', true)->count();
$counts['media_files'] = count(Illuminate\Support\Facades\File::allFiles(config('media.root').'/images'));
echo json_encode(['framework' => app()->version(), 'counts' => $counts, 'search' => $results, 'sandbox' => config('store.sandbox')], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
