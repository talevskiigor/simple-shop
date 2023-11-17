<?php

namespace App\Helpers;

class Image
{

    public static function get(string $slug, int|null $width = null, int|null $height = null, int $quality = 100): string
    {
        $imagepath = '/media-resize/' . $slug . sprintf('?w=%s&%s&q=%s', $width, $height, $quality);
        return url($imagepath);
    }

}
