<?php

namespace Tests\Feature;

use App\Helpers\ShoppingCart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_and_category_show_stocked_products_and_preserve_urls(): void
    {
        $category = Category::factory()->create(['slug' => 'test-category']);
        $available = Product::factory()->create(['slug' => 'test-product']);
        $soldOut = Product::factory()->create(['quantity' => 0]);
        $category->product()->attach([$available->id, $soldOut->id]);

        foreach (['/', '/categories/test-category'] as $url) {
            $this->get($url)->assertOk()
                ->assertViewHas('items', fn ($items) => $items->modelKeys() === [$available->id])
                ->assertSee('/product/test-product', false);
        }
        $this->get('/categories/missing-category')->assertNotFound();
    }

    public function test_product_and_content_page_render_their_content(): void
    {
        $product = Product::factory()->create(['description' => '<p>Опис на производот.</p>']);
        $page = Page::factory()->create(['body' => '<p>Информации за достава.</p>']);
        $this->get('/product/'.$product->slug)->assertOk()->assertSee('Опис на производот.');
        $this->get('/pages/'.$page->slug)->assertOk()->assertSee('Информации за достава.');
    }

    public function test_guest_cart_preserves_session_add_remove_and_one_unit_rule(): void
    {
        $product = Product::factory()->create();
        $this->get('/cart')->assertOk()->assertSee('Немате избрано продукти.');
        $this->post('/cart', ['productId' => $product->id])->assertRedirect('/cart');
        $id = session(ShoppingCart::SHOPPING_CART_ID);
        $this->post('/cart', ['productId' => $product->id])->assertRedirect('/cart');
        $this->assertSame($id, session(ShoppingCart::SHOPPING_CART_ID));
        $this->get('/cart')->assertOk()->assertViewHas('cart', fn ($cart) => $cart->getTotalQuantity() === 1 && (float) $cart->getTotal() === 1000.0);
        $this->delete('/cart/'.$product->id)->assertRedirect('/cart');
        $this->get('/cart')->assertViewHas('cart', fn ($cart) => $cart->isEmpty());
        $this->assertGuest();
    }

    public function test_empty_cart_redirects_away_from_checkout_and_sold_out_product_is_not_added(): void
    {
        $product = Product::factory()->create(['quantity' => 0]);
        $this->get('/order')->assertRedirect('/cart');
        $this->post('/cart', ['productId' => $product->id])->assertRedirect('/cart');
        $this->get('/cart')->assertViewHas('cart', fn ($cart) => $cart->isEmpty());
    }

    public function test_guest_order_saves_delivery_and_items_without_login_or_payment(): void
    {
        $product = Product::factory()->create();
        $this->post('/cart', ['productId' => $product->id]);
        $this->get('/order')->assertOk();
        $this->post('/order', $this->delivery())->assertOk()
            ->assertSee('payments are disabled')->assertDontSee('cpay.com.mk');
        $this->assertDatabaseCount('orders', 1);
        $order = Order::firstOrFail();
        $this->assertSame('guest@example.test', $order->email);
        $this->assertEquals(1000, $order->total);
        $this->assertEquals($product->id, array_values(json_decode($order->items, true))[0]['id']);
        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertGuest();
    }

    public function test_invalid_guest_details_do_not_create_an_order(): void
    {
        $product = Product::factory()->create();
        $this->post('/cart', ['productId' => $product->id]);
        $this->from('/order')->post('/order', array_merge($this->delivery(), ['email' => 'bad', 'phone' => 'abc']))
            ->assertRedirect('/order')->assertSessionHasErrors(['email', 'phone']);
        $this->assertDatabaseCount('orders', 0);
    }

    private function delivery(): array
    {
        return ['first' => 'Test', 'last' => 'Guest', 'address' => 'Test address', 'city' => 'Test city', 'phone' => '070000000', 'email' => 'guest@example.test'];
    }
}
