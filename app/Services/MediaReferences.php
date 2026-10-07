<?php
namespace App\Services;

use App\Models\{Media, Product, Page};
use Illuminate\Support\Facades\{DB, File};

class MediaReferences
{
    /** Includes archived content and historical order snapshots so cleanup cannot break them. */
    public function inventory(): array
    {
        $references = [];
        $texts = [];
        foreach (Product::withTrashed()->get(['id', 'image', 'description']) as $product) {
            if ($product->image) $references[ltrim($product->image, '/') ][] = 'product:'.$product->id;
            $texts[] = (string) $product->description;
        }
        foreach (DB::table('media_product')->join('media', 'media.id', '=', 'media_product.media_id')->pluck('media.path') as $path) {
            if ($path) $references[ltrim($path, '/')][] = 'gallery';
        }
        foreach (Page::withTrashed()->pluck('body') as $text) $texts[] = $text;
        foreach (DB::table('orders')->pluck('items') as $text) $texts[] = $text;
        foreach ([resource_path('views'), resource_path('scss'), resource_path('js')] as $directory) {
            foreach (File::allFiles($directory) as $file) $texts[] = $file->getContents();
        }
        foreach (DB::table('media_aliases')->pluck('target') as $path) $references[$path][] = 'published-alias';
        $content = rawurldecode(html_entity_decode(implode("\n", $texts), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $paths = Media::withTrashed()->pluck('path')->filter()->all();
        $root = config('media.root');
        if (is_dir($root.'/images')) foreach (File::allFiles($root.'/images') as $file) $paths[] = 'images/'.$file->getRelativePathname();
        foreach (array_unique($paths) as $path) {
            // Conservative basename matching also protects old OpenCart URLs embedded in HTML/snapshots.
            if (str_contains($content, basename($path))) $references[ltrim($path, '/')][] = 'content-or-template';
        }
        return $references;
    }

    public function used(string $path): bool { return isset($this->inventory()[ltrim($path, '/')]); }
}
