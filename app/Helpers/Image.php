<?php

namespace App\Helpers;

class Image
{

    const W100 = 100;
    const W200 = 200;
    const W256 = 256;

    public static function get(string $slug, int|null $width = null, int|null $height = null, int $quality = 100): string
    {
        $slug = ltrim(str_replace(public_path('media/'), '', $slug), '/');
        if (!$slug) return url('/assets/image-unavailable.svg');
        $path = implode('/', array_map('rawurlencode', explode('/', $slug)));
        return url('/media-resize/'.$path).'?'.http_build_query(['w' => $width, 'h' => $height, 'q' => $quality]);
    }


    public static function isImage($file):bool
    {
        $allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp','image/svg','image/webp'];
        $type = mime_content_type($file);
        return in_array($type,$allowedMimeTypes);
    }
}
