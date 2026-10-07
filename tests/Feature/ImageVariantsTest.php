<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, File};
use Tests\TestCase;

class ImageVariantsTest extends TestCase
{
    use RefreshDatabase;
    private string $root;
    protected function setUp(): void {
        parent::setUp(); $this->root = sys_get_temp_dir().'/forkids-image-'.bin2hex(random_bytes(8));
        File::ensureDirectoryExists($this->root.'/images');
        config(['media.root' => $this->root, 'media.cache' => $this->root.'/cache']);
        $this->source('multi.part.png');
    }
    protected function tearDown(): void { File::deleteDirectory($this->root); parent::tearDown(); }
    private function source(string $name, int $color = 100): void {
        $image = imagecreatetruecolor(400, 200); imagefill($image, 0, 0, imagecolorallocate($image, $color, 10, 200)); imagepng($image, $this->root.'/images/'.$name); imagedestroy($image);
    }
    public function test_resize_uses_safe_dimensions_cache_etags_and_source_invalidation(): void {
        $first = $this->get('/media-resize/images/multi.part.png?w=100&q=80')->assertOk()->assertHeader('content-type','image/webp');
        $file = $first->baseResponse->getFile()->getPathname();
        $this->assertSame([100, 50], array_slice(getimagesize($file), 0, 2));
        $this->get('/media-resize/images/multi.part.png?w=100&q=80')->assertOk();
        $this->assertCount(1, glob($this->root.'/cache/*.webp'));
        $this->withHeader('If-None-Match', $first->headers->get('etag'))->get('/media-resize/images/multi.part.png?w=100&q=80')->assertStatus(304);
        $this->flushHeaders(); $this->source('multi.part.png', 200);
        $this->get('/media-resize/images/multi.part.png?w=100&q=80')->assertOk();
        $this->assertCount(2, glob($this->root.'/cache/*.webp'));
        $this->get('/media-resize/images/multi.part.png?w=100&q=40')->assertOk();
        $this->assertCount(3, glob($this->root.'/cache/*.webp'));
    }
    public function test_missing_images_have_placeholder_and_invalid_paths_or_sizes_are_rejected(): void {
        $this->get('/media-resize/images/missing.jpg?w=100')->assertOk()->assertHeader('content-type', 'image/svg+xml');
        $this->getJson('/media-resize/images/multi.part.png?w=999999')->assertUnprocessable();
        $this->getJson('/media-resize/images/multi.part.png?w[]=100')->assertUnprocessable();
        $this->get('/media-resize/.env?w=100')->assertNotFound();
        $this->get('/media-resize/images/%2e%2e/%2eenv?w=100')->assertNotFound();
        file_put_contents($this->root.'/outside.jpg', 'private'); symlink($this->root.'/outside.jpg', $this->root.'/images/link.jpg');
        // A symlink outside the media root is never followed.
        unlink($this->root.'/images/link.jpg'); symlink('/etc/passwd', $this->root.'/images/link.jpg');
        $this->get('/media/images/link.jpg')->assertNotFound();
    }
    public function test_retired_logo_urls_redirect_to_canonical_media(): void {
        $this->get('/logo/visa-128X_null_100.webp')->assertRedirect(\App\Helpers\Image::get('images/visa.png', 128));
        $this->get('/logo/unknown-file.png')->assertNotFound();
    }
    public function test_aliases_preserve_published_media_urls(): void {
        DB::table('media_aliases')->insert(['path' => 'images/old.png','target'=>'images/multi.part.png','path_hash'=>hash('sha256','images/old.png')]);
        $this->get('/media/images/old.png')->assertOk()->assertHeader('content-type','image/png');
        $this->get('/media-resize/images/old.png?w=100')->assertOk()->assertHeader('content-type','image/webp');
    }
}
