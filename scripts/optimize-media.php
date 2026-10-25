<?php
// Offline only: creates a separate candidate tree; never modifies source files.
// Usage: php scripts/optimize-media.php SOURCE NEW_OUTPUT_DIRECTORY
if (PHP_SAPI !== 'cli' || $argc !== 3) { fwrite(STDERR, "Expected source and new output directory.\n"); exit(1); }
$root = realpath($argv[1]);
$output = $argv[2];
if (!$root || !is_dir($root) || file_exists($output)) throw new RuntimeException('Source must exist and output must be new.');
if (!mkdir($output, 0700, true)) throw new RuntimeException('Cannot create output.');
$rows = []; $before = 0; $after = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
    if ($file->isLink()) throw new RuntimeException('Symlinks are not supported.');
    if (!$file->isFile()) continue;
    $source = $file->getPathname(); $relative = substr($source, strlen($root) + 1); $target = $output.'/'.$relative;
    if (!is_dir(dirname($target))) mkdir(dirname($target), 0755, true);
    $hash = hash_file('sha256', $source); $bytes = filesize($source); $info = @getimagesize($source);
    $mime = $info['mime'] ?? ''; $changed = false;
    // Preserve unsupported, animated and unusually large files byte-for-byte.
    $animated = in_array($mime, ['image/webp', 'image/png']) && preg_match('/ANIM|acTL/', file_get_contents($source));
    if ($info && $info[0] * $info[1] <= 25000000 && !$animated && in_array($mime, ['image/jpeg','image/png','image/webp'])) {
        $image = match ($mime) { 'image/jpeg' => imagecreatefromjpeg($source), 'image/png' => imagecreatefrompng($source), 'image/webp' => imagecreatefromwebp($source) };
        if (!$image) throw new RuntimeException('Decode failed: '.$relative);
        try {
            if ($mime === 'image/jpeg') {
                $orientation = (int) (@exif_read_data($source)['Orientation'] ?? 1);
                if (in_array($orientation, [2,4,5,7])) imageflip($image, IMG_FLIP_HORIZONTAL);
                $angle = match ($orientation) { 3,4 => 180, 5,8 => 90, 6,7 => -90, default => 0 };
                if ($angle) { $rotated = imagerotate($image, $angle, 0); unset($image); $image = $rotated; unset($rotated); }
            }
            $ratio = min(1, 2560 / max(imagesx($image), imagesy($image)));
            $w = max(1, (int) round(imagesx($image) * $ratio)); $h = max(1, (int) round(imagesy($image) * $ratio));
            $small = imagecreatetruecolor($w, $h);
            try {
                imagealphablending($small, false); imagesavealpha($small, true);
                imagefill($small, 0, 0, imagecolorallocatealpha($small, 255, 255, 255, 127));
                imagecopyresampled($small, $image, 0,0,0,0,$w,$h,imagesx($image),imagesy($image));
                $ok = match ($mime) { 'image/jpeg' => imagejpeg($small, $target, 88), 'image/png' => imagepng($small, $target, 9), 'image/webp' => imagewebp($small, $target, 85) };
                if (!$ok || !($check = getimagesize($target)) || $check[0] !== $w || $check[1] !== $h) throw new RuntimeException('Invalid candidate: '.$relative);
                $changed = filesize($target) < $bytes;
            } finally { unset($small); }
        } finally { unset($image); }
    }
    if (!$changed && !copy($source, $target)) throw new RuntimeException('Copy failed: '.$relative);
    chmod($target, 0644);
    if (hash_file('sha256', $source) !== $hash) throw new RuntimeException('Source changed during optimization.');
    $size = filesize($target); $before += $bytes; $after += $size;
    $rows[] = ['path'=>$relative,'original_sha256'=>$hash,'optimized_sha256'=>hash_file('sha256',$target),'before'=>$bytes,'after'=>$size,'changed'=>$changed];
}
file_put_contents($output.'.manifest.json', json_encode(['files'=>$rows,'before'=>$before,'after'=>$after], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
chmod($output.'.manifest.json',0600);
echo json_encode(['files'=>count($rows),'before'=>$before,'after'=>$after,'saved'=>$before-$after]),PHP_EOL;
