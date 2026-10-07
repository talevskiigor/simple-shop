<?php

namespace Tests\Feature;

use App\Http\Middleware\NonProductionSafety;
use App\Models\{Category, Page, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SearchIndexingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_indexing_keeps_sandbox_and_payment_safeguards(): void
    {
        config(['store.allow_indexing' => true, 'payments.enabled' => true]);

        $this->get('/')->assertOk()->assertHeaderMissing('X-Robots-Tag')
            ->assertHeader('Content-Security-Policy', "form-action 'self' https://www.cpay.com.mk; frame-src 'none'")
            ->assertDontSee('connect.facebook.net', false);
        $this->get('/update')->assertNotFound();
        $this->post('/admin/register')->assertNotFound();
        $this->get('/admin/product')->assertRedirect('/admin/login');
    }

    public function test_private_flows_stay_noindex_when_the_catalog_is_indexable(): void
    {
        config(['store.allow_indexing' => true, 'payments.enabled' => true]);

        foreach (['/admin', '/admin/login', '/cart', '/order', '/bank/ok', '/payment/result/test'] as $path) {
            $response = (new NonProductionSafety())->handle(Request::create($path), fn () => response('Private'));
            $this->assertSame('noindex, nofollow, noarchive', $response->headers->get('X-Robots-Tag'), $path);
        }
    }

    public function test_sitemap_contains_only_current_public_urls_on_the_canonical_domain(): void
    {
        config(['store.allow_indexing' => true]);
        URL::forceRootUrl('https://forkids.mk');
        URL::forceScheme('https');
        $product = Product::factory()->create(['quantity' => 0]);
        Product::factory()->create(['active' => false]);
        Product::factory()->create()->delete();
        $category = Category::factory()->create();
        Category::factory()->create()->delete();
        $page = Page::factory()->create(['published' => true]);
        Page::factory()->create(['published' => false]);
        Page::factory()->create(['published' => true])->delete();

        $response = $this->get('/sitemap.xml')->assertOk()->assertHeaderMissing('X-Robots-Tag');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $xml->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urls = array_map('strval', $xml->xpath('/sm:urlset/sm:url/sm:loc'));
        $this->assertEqualsCanonicalizing([
            'https://forkids.mk',
            'https://forkids.mk/contact',
            'https://forkids.mk/categories/'.$category->slug,
            'https://forkids.mk/pages/'.$page->slug,
            'https://forkids.mk/product/'.$product->slug,
        ], $urls);

        $page->update(['published' => false]);
        $this->get('/sitemap.xml')->assertOk()->assertDontSee('/pages/'.$page->slug, false);
    }
}
