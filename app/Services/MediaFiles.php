<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class MediaFiles
{
    public function normalize(string $path): string
    {
        $path = ltrim($path, '/');
        abort_if(str_contains($path, '\\') || preg_match('/[\x00-\x1f\x7f]/', $path), 404);
        abort_unless(str_starts_with($path, 'images/'), 404);
        foreach (explode('/', $path) as $part) abort_if($part === '' || $part === '.' || $part === '..', 404);
        return $path;
    }

    public function resolve(string $path): ?string
    {
        $path = $this->normalize($path);
        // Alias records preserve previously published URLs after duplicate cleanup.
        $path = DB::table('media_aliases')->where('path_hash', hash('sha256', $path))->value('target') ?? $path;
        $path = $this->normalize($path);
        $root = realpath(config('media.root'));
        $file = realpath(config('media.root').'/'.$path);
        return $root && $file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file) ? $file : null;
    }

    public function url(string $path): string
    {
        return '/media/'.implode('/', array_map('rawurlencode', explode('/', $this->normalize($path))));
    }
}
