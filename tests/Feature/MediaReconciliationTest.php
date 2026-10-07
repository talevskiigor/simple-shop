<?php
namespace Tests\Feature;

use App\Models\{Media, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Artisan, DB, File};
use Tests\TestCase;

class MediaReconciliationTest extends TestCase
{
    use RefreshDatabase;
    public function test_cleanup_archives_unused_files_deduplicates_references_and_keeps_missing_media_visible(): void
    {
        $root = sys_get_temp_dir().'/forkids-reconcile-'.bin2hex(random_bytes(8));
        File::ensureDirectoryExists($root.'/media/images');
        config(['media.root' => $root.'/media']);
        try {
            $image = imagecreatetruecolor(10, 10); imagepng($image, $root.'/media/images/a.png'); imagedestroy($image);
            copy($root.'/media/images/a.png', $root.'/media/images/b.png');
            file_put_contents($root.'/media/images/unused.txt', 'Unused original');
            $a = Media::create(['name'=>'a', 'path'=>'images/a.png','file'=>'a.png','type'=>'image']);
            $b = Media::create(['name'=>'b', 'path'=>'images/b.png','file'=>'b.png','type'=>'image']);
            $missing = Media::create(['name'=>'missing', 'path'=>'images/missing.png','file'=>'missing.png','type'=>'image']);
            $product = Product::factory()->create(['image'=>'images/b.png']);
            $product->media()->attach([$a->id, $b->id, $missing->id]);
            $this->assertSame(0, Artisan::call('media:reconcile'));
            $this->assertFileExists($root.'/media/images/unused.txt');
            $this->assertSame(0, Artisan::call('media:reconcile', ['--apply'=>true,'--backup'=>$root.'/recovery']));
            $this->assertFileExists($root.'/media/images/a.png');
            $this->assertFileDoesNotExist($root.'/media/images/b.png');
            $this->assertFileDoesNotExist($root.'/media/images/unused.txt');
            $this->assertFileExists($root.'/recovery/originals/images/b.png');
            $this->assertFileExists($root.'/recovery/completed.json');
            $this->assertSame('images/a.png', $product->fresh()->image);
            $this->assertCount(2, $product->fresh()->media);
            $this->assertNotNull(Media::find($missing->id));
            $this->assertDatabaseHas('media_aliases',['path'=>'images/b.png','target'=>'images/a.png']);
            $this->get('/media/images/b.png')->assertOk();
            Artisan::call('media:reconcile'); $plan = json_decode(Artisan::output(), true);
            $this->assertSame(0, $plan['remove_unused']);
            $this->assertSame(0, $plan['remove_duplicates']);
            $this->assertContains('images/missing.png', $plan['missing_referenced_paths']);
        } finally { File::deleteDirectory($root); }
    }
}
