<?php

namespace App\Helpers;

class Image
{

    const W100 = 100;
    const W200 = 200;
    const W256 = 256;

    public static function get(string $slug, int|null $width = null, int|null $height = null, int $quality = 100): string
    {
        $slug = str_replace(public_path('media/'),'',$slug);
        $imagepath = '/media-resize/' . $slug . sprintf('?w=%s&h=%s&q=%s', $width, $height, $quality);
        return url($imagepath);
    }


    public static function isImage($file):bool
    {
        $allowedMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp','image/svg','image/webp'];
        $type = mime_content_type($file);
        return in_array($type,$allowedMimeTypes);
    }
}
