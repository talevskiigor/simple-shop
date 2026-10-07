<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageVariants
{
    public function response(Request $request, string $path)
    {
        $data = $request->validate(['w' => 'nullable|integer|min:1|max:1600', 'h' => 'nullable|integer|min:1|max:1600', 'q' => 'nullable|integer|min:1|max:100']);
        $width = $this->size($data['w'] ?? (empty($data['h']) ? 768 : null));
        $height = $this->size($data['h'] ?? null);
        $quality = min(95, max(30, (int) ($data['q'] ?? 80)));
        $source = app(MediaFiles::class)->resolve($path);
        if (!$source) return $this->placeholder();
        $info = @getimagesize($source);
        if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp', 'image/gif']) || $info[0] * $info[1] > config('media.max_pixels')) return $this->placeholder();
        $key = hash('sha256', 'v2|'.$path.'|'.hash_file('sha256', $source)."|$width|$height|$quality");
        $directory = config('media.cache');
        File::ensureDirectoryExists($directory);
        $target = $directory.'/'.$key.'.webp';
        if (!is_file($target)) {
            $lock = fopen($directory.'/'.$key.'.lock', 'c');
            abort_unless($lock && flock($lock, LOCK_EX), 503);
            try {
                if (!is_file($target)) $this->render($source, $info['mime'], $target, $width, $height, $quality);
            } finally {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
        $response = response()->file($target, ['Content-Type' => 'image/webp', 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'public, max-age=3600']);
        $response->setEtag($key);
        $response->isNotModified($request);
        return $response;
    }

    private function size(?int $value): ?int
    {
        if (!$value) return null;
        foreach (config('media.widths') as $size) if ($size >= $value) return $size;
        return 1600;
    }

    private function render(string $source, string $mime, string $target, ?int $width, ?int $height, int $quality): void
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($source), 'image/png' => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source), 'image/gif' => @imagecreatefromgif($source),
        };
        abort_unless($image, 422, 'Image cannot be decoded.');
        try {
            if ($mime === 'image/jpeg') {
                $orientation = (int) (@exif_read_data($source)['Orientation'] ?? 1);
                if (in_array($orientation, [2, 4, 5, 7])) imageflip($image, IMG_FLIP_HORIZONTAL);
                $angle = match ($orientation) {3, 4 => 180, 5, 8 => 90, 6, 7 => -90, default => 0};
                if ($angle) { $rotated = imagerotate($image, $angle, 0); imagedestroy($image); $image = $rotated; }
            }
            $ratio = min(1, $width ? $width / imagesx($image) : 1, $height ? $height / imagesy($image) : 1);
            $w = max(1, (int) round(imagesx($image) * $ratio));
            $h = max(1, (int) round(imagesy($image) * $ratio));
            $output = imagecreatetruecolor($w, $h);
            imagealphablending($output, false);
            imagesavealpha($output, true);
            imagefill($output, 0, 0, imagecolorallocatealpha($output, 255, 255, 255, 127));
            imagecopyresampled($output, $image, 0, 0, 0, 0, $w, $h, imagesx($image), imagesy($image));
            $temporary = tempnam(dirname($target), '.resize-');
            try {
                if (!imagewebp($output, $temporary, $quality) || !rename($temporary, $target)) throw new \RuntimeException('Unable to save image variant.');
                chmod($target, 0644);
            } finally { imagedestroy($output); if (is_file($temporary)) unlink($temporary); }
        } finally { imagedestroy($image); }
    }

    public function placeholder()
    {
        return response()->file(public_path('assets/image-unavailable.svg'), ['Content-Type' => 'image/svg+xml', 'Cache-Control' => 'no-cache', 'X-Content-Type-Options' => 'nosniff']);
    }
}
