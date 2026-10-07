<?php
namespace Tests\Feature;

use App\Models\{User, Product, Category, Page, Media};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{File, Hash};
use Tests\TestCase;

class NativeAdministrationTest extends TestCase
{
    use RefreshDatabase;
    private string $mediaRoot;
    protected function setUp(): void {
        parent::setUp();
        $this->mediaRoot = sys_get_temp_dir().'/forkids-test-'.bin2hex(random_bytes(8));
        config(['media.root' => $this->mediaRoot, 'media.cache' => $this->mediaRoot.'/cache']);
        File::ensureDirectoryExists($this->mediaRoot.'/images');
    }
    protected function tearDown(): void { File::deleteDirectory($this->mediaRoot); parent::tearDown(); }

    public function test_only_administrators_can_login_or_access_management(): void {
        $user = User::factory()->create(['is_admin' => false]);
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        foreach (['/admin', '/admin/product', '/admin/categories', '/admin/pages', '/admin/media', '/admin/users', '/admin/orders'] as $url) $this->actingAs($user)->get($url)->assertForbidden();
        config(['store.sandbox' => false]);
        $this->get('/admin/register')->assertNotFound();
        $this->post('/admin/register')->assertNotFound();
    }

    public function test_admin_can_manage_accounts_but_cannot_remove_self(): void {
        $admin = User::factory()->create();
        $this->actingAs($admin)->post('/admin/users', ['name' => 'New administrator', 'email' => 'new@example.test', 'password' => 'LongPassword123!', 'password_confirmation' => 'LongPassword123!'])->assertRedirect('/admin/users');
        $new = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertTrue($new->is_admin);
        $this->assertTrue(Hash::check('LongPassword123!', $new->password));
        $this->delete('/admin/users/'.$admin->id)->assertUnprocessable();
        $this->delete('/admin/users/'.$new->id)->assertRedirect('/admin/users');
        $this->assertDatabaseMissing('users', ['id' => $new->id]);
        $this->get('/admin/logout')->assertStatus(405);
    }

    public function test_admin_pages_render_and_new_products_require_valid_gallery(): void {
        $this->actingAs(User::factory()->create());
        foreach (['/admin', '/admin/product', '/admin/product/create', '/admin/categories', '/admin/categories/create', '/admin/pages', '/admin/pages/create', '/admin/media', '/admin/users', '/admin/users/create', '/admin/orders'] as $url) $this->get($url)->assertOk();
        $data = $this->productData(); unset($data['media_ids']);
        $this->post('/admin/product', $data)->assertSessionHasErrors('media_ids');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_upload_deduplicates_rejects_active_content_and_protects_used_media(): void {
        $this->actingAs(User::factory()->create());
        $file = UploadedFile::fake()->image('example.jpg', 100, 80);
        $first = $this->postJson('/admin/media', ['file' => $file])->assertCreated()->json();
        $second = $this->postJson('/admin/media', ['file' => $file])->assertCreated()->json();
        $this->assertSame($first['id'], $second['id']);
        $this->assertDatabaseCount('media', 1);
        $this->postJson('/admin/media', ['file' => UploadedFile::fake()->createWithContent('attack.svg', '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"/>')])->assertUnprocessable();
        $this->postJson('/admin/media', ['file' => UploadedFile::fake()->image('huge.jpg', 5000, 5000)])->assertUnprocessable();
        Product::factory()->create(['image' => $first['path']]);
        $this->delete('/admin/media/'.$first['id'])->assertSessionHasErrors('media');
        $this->assertNotNull(Media::find($first['id']));
    }

    public function test_native_product_creation_preserves_cyrillic_html_gallery_order_and_visibility(): void {
        $this->actingAs(User::factory()->create());
        $data = $this->productData();
        $this->post('/admin/product', $data)->assertSessionHasNoErrors()->assertRedirect();
        $product = Product::firstOrFail();
        $this->assertSame('Тест производ', $product->name);
        $this->assertSame($data['media_ids'], $product->media->pluck('id')->all());
        $this->assertStringNotContainsString('<script', $product->description);
        $this->assertStringNotContainsString('onerror', $product->description);
        $this->get('/product/'.$product->slug)->assertOk()->assertSee('Опис');
        $this->put('/admin/product/'.$product->id, [...$data, 'active' => 0])->assertSessionHasNoErrors();
        $this->get('/product/'.$product->slug)->assertNotFound();
        $this->delete('/admin/product/'.$product->id)->assertRedirect('/admin/product');
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_page_publishing_sanitizes_links_and_embeds_without_decoding_script_back_into_html(): void {
        $this->actingAs(User::factory()->create());
        $data = ['title' => 'Информации', 'slug' => 'informacii', 'body' => '<h2>Достава</h2><script>alert(1)</script><a href="javascript:alert(1)">bad link</a><video src="/media/images/video.mp4" controls></video><img src="/media/images/test.jpg" onerror="alert(1)">', 'published' => 0];
        $this->post('/admin/pages', $data)->assertSessionHasNoErrors();
        $page = Page::firstOrFail();
        $this->get('/pages/informacii')->assertNotFound();
        $this->put('/admin/pages/'.$page->id, [...$data, 'published' => 1])->assertSessionHasNoErrors();
        $this->get('/pages/informacii')->assertOk()->assertSee('Достава')->assertDontSee('<script>alert(1)', false)->assertDontSee('javascript:', false)->assertDontSee('onerror', false)->assertSee('<video', false);
    }

    public function test_video_upload_embeds_and_supports_partial_downloads(): void {
        $this->actingAs(User::factory()->create());
        $video = new UploadedFile(base_path('tests/Fixtures/sample.mp4'), 'sample.mp4', 'video/mp4', null, true);
        $data = $this->postJson('/admin/media', ['file' => $video])->assertCreated()->json();
        $this->assertSame('video', $data['type']);
        $this->withHeader('Range', 'bytes=0-7')->get($data['url'])->assertStatus(206)->assertHeader('Content-Type', 'video/mp4');
        $this->flushHeaders();
        $this->post('/admin/pages', ['title' => 'Video page', 'slug' => 'video-page', 'published' => 1, 'body' => '<video controls src="'.$data['url'].'"></video>'])->assertSessionHasNoErrors();
        $this->get('/pages/video-page')->assertOk()->assertSee('<video', false)->assertSee($data['url'], false);
    }

    private function productData(): array {
        $category = Category::factory()->create();
        $media = [];
        foreach (['a.jpg', 'b.png'] as $name) $media[] = $this->postJson('/admin/media', ['file' => UploadedFile::fake()->image($name, $name === 'a.jpg' ? 100 : 120, 80)])->assertCreated()->json('id');
        return ['name' => 'Тест производ', 'slug' => 'test-proizvod', 'model' => 'TEST-1', 'description' => '<p>Опис</p><script>alert(1)</script><img src="/media/images/a.jpg" onerror="bad()">', 'price' => '1000.50', 'discount' => '20', 'quantity' => 5, 'active' => 1, 'category_ids' => [$category->id], 'media_ids' => array_reverse($media)];
    }
}
