<?php
namespace Tests\Feature;
use App\Models\{Order, Product, PaymentAttempt};
use App\Services\Payments\{Cpay, CpayChecksum};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PaymentTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); config(['payments.enabled' => true, 'payments.merchant_id' => '1234567890', 'payments.merchant_name' => 'Test Store', 'payments.secret' => 'TEST_PASS', 'payments.test_amount_mkd' => '1']); }
    private function attempt(): PaymentAttempt { return app(Cpay::class)->prepare(Order::factory()->create(['total' => 7500, 'email' => 'customer@example.test'])); }
    private function returnFields(PaymentAttempt $attempt, bool $reference = true): array {
        $f = $attempt->request_fields; $keys = array_keys($f); $fields = [$keys[1] => $f[$keys[1]], $keys[0] => $f[$keys[0]], ...array_slice($f, 2, null, true)];
        if ($reference) $fields['cPayPaymentRef'] = '987654321';
        $s = app(CpayChecksum::class)->sign($fields, 'TEST_PASS');
        return [...$fields, 'ReturnCheckSumHeader' => $s['header'], 'ReturnCheckSum' => $s['checksum']];
    }
    public function test_one_denar_overrides_only_charge_and_preserves_real_order(): void {
        $a = $this->attempt(); $fields = app(Cpay::class)->form($a);
        $this->assertSame('100', $fields['AmountToPay']); $this->assertTrue($a->is_test);
        $this->assertEquals(7500, $a->order->total); $this->assertSame('MKD', $fields['AmountCurrency']);
        config(['payments.test_amount_mkd' => null]);
        $this->assertSame(100, app(Cpay::class)->prepare($a->order)->amount_minor);
        $normal = app(Cpay::class)->prepare(Order::factory()->create(['total' => 2500]));
        $this->assertSame(250000, $normal->amount_minor); $this->assertFalse($normal->is_test);
    }
    public function test_signed_return_is_idempotent_and_never_fulfills_test_orders(): void {
        $p = Product::factory()->create(['quantity' => 5]); $a = $this->attempt();
        $body = $this->returnFields($a);
        $this->post('/bank/ok?token='.$a->return_token, $body)->assertStatus(303);
        $this->post('/bank/ok?token='.$a->return_token, $body)->assertStatus(303);
        $this->post('/bank/fail?token='.$a->return_token, $body)->assertStatus(303);
        $this->assertSame('returned_success', $a->fresh()->status); $this->assertEquals(5, $p->fresh()->quantity);
        $this->assertFalse((bool) $a->order->fresh()->finished);
        $this->get('/payment/result/'.$a->return_token)->assertOk()->assertSee('1 денар')->assertDontSee($a->order->email);
    }
    public function test_unsigned_tampered_and_reflected_bank_callbacks_are_rejected(): void {
        $a = $this->attempt(); $body = $this->returnFields($a);
        $this->post('/bank/ok?token='.$a->return_token, ['Details2' => $a->reference])->assertUnprocessable();
        $this->post('/bank/ok?token='.$a->return_token, [...$body, 'AmountToPay' => '200'])->assertUnprocessable();
        $this->post('/bank/ok?token=wrong', $body)->assertUnprocessable();
        $f = app(Cpay::class)->form($a);
        $this->post('/bank/fail?token='.$a->return_token, [...$f, 'ReturnCheckSumHeader' => $f['CheckSumHeader'], 'ReturnCheckSum' => $f['CheckSum']])->assertUnprocessable();
        $this->assertSame('pending', $a->fresh()->status);
    }
    public function test_cancel_without_bank_reference_is_recorded_and_cart_is_unchanged(): void {
        $a = $this->attempt(); $this->withSession(['cart_v2' => [123]])->post('/bank/fail?token='.$a->return_token, $this->returnFields($a, false))->assertStatus(303)->assertSessionHas('cart_v2', [123]);
        $this->assertSame('failed', $a->fresh()->status);
        $this->assertNotSame($a->reference, app(Cpay::class)->prepare($a->order)->reference);
    }
    public function test_signatures_count_unicode_characters_and_reject_duplicate_parameters(): void {
        $f = ['AmountToPay'=>'100','PayToMerchant'=>'123','MerchantName'=>'Тест','AmountCurrency'=>'MKD','Details1'=>'Нарачка','Details2'=>'A123','PaymentOKURL'=>'https://example.test/ok','PaymentFailURL'=>'https://example.test/fail'];
        $s = app(CpayChecksum::class)->sign($f,'TEST_PASS');
        $this->assertSame($f, app(CpayChecksum::class)->verify([...$f,'ReturnCheckSumHeader'=>$s['header'],'ReturnCheckSum'=>$s['checksum']],'TEST_PASS'));
        $this->assertStringStartsWith('08AmountToPay,', $s['header']);
    }
    public function test_admin_confirmation_is_explicit_and_test_orders_never_reduce_stock(): void {
        $p = Product::factory()->create(['quantity' => 5]); $a = $this->attempt();
        $this->post('/bank/ok?token='.$a->return_token, $this->returnFields($a))->assertStatus(303);
        $this->post('/admin/payments/'.$a->id.'/confirm', ['bank_verified'=>1])->assertRedirect('/admin/login');
        $this->actingAs(\App\Models\User::factory()->create());
        $this->post('/admin/payments/'.$a->id.'/confirm')->assertSessionHasErrors('bank_verified');
        $this->post('/admin/payments/'.$a->id.'/confirm', ['bank_verified'=>1])->assertRedirect('/admin/orders');
        $this->assertSame('confirmed', $a->fresh()->status);
        $this->assertFalse((bool) $a->order->fresh()->finished);
        $this->assertEquals(5, $p->fresh()->quantity);
    }
    public function test_normal_confirmations_decrement_stock_once_and_preserve_paid_state(): void {
        config(['payments.test_amount_mkd' => null]);
        $p = Product::factory()->create(['quantity'=>2]);
        $order = Order::factory()->create(['items'=>json_encode([['id'=>$p->id,'quantity'=>1]]),'total'=>1000]);
        $a = app(Cpay::class)->prepare($order);
        $this->post('/bank/ok?token='.$a->return_token, $this->returnFields($a))->assertStatus(303);
        $this->actingAs(\App\Models\User::factory()->create());
        $this->post('/admin/payments/'.$a->id.'/confirm', ['bank_verified'=>1])->assertRedirect('/admin/orders');
        $this->post('/admin/payments/'.$a->id.'/confirm', ['bank_verified'=>1])->assertRedirect('/admin/orders');
        $this->post('/bank/fail?token='.$a->return_token, $this->returnFields($a))->assertStatus(303);
        $this->assertEquals(1, $p->fresh()->quantity); $this->assertTrue((bool) $order->fresh()->finished);
        $this->assertSame('confirmed', $a->fresh()->status);
    }
    public function test_real_guest_cart_remains_unchanged_while_checkout_charges_one_denar(): void {
        config(['store.allow_indexing' => true]);
        \Illuminate\Support\Facades\URL::forceRootUrl('https://forkids.mk');
        \Illuminate\Support\Facades\URL::forceScheme('https');
        $p = Product::factory()->create(['price'=>1500, 'discount'=>20]);
        $this->post('/cart', ['productId'=>$p->id]);
        $data = ['first'=>'Тест', 'last'=>'Купувач', 'address'=>'Тест адреса', 'city'=>'Скопје', 'phone'=>'070000000', 'email'=>'guest@example.test'];
        $this->post('/order', $data)->assertOk()->assertSee('1 денар вкупно')->assertSee('name=\'AmountToPay\' value=\'100\'', false)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $order = Order::firstOrFail(); $this->assertEquals(1200, $order->total); $original = PaymentAttempt::firstOrFail();
        $this->assertStringStartsWith('https://forkids.mk/bank/ok?token=', $original->request_fields['PaymentOKURL']);
        $this->assertStringStartsWith('https://forkids.mk/bank/fail?token=', $original->request_fields['PaymentFailURL']);
        $this->post('/order', $data)->assertOk();
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('payment_attempts', 1);
        $this->post('/order', [...$data, 'address'=>'Corrected address'])->assertOk();
        $this->assertSame('Тест адреса', $order->fresh()->address);
        $this->assertSame(100, $original->fresh()->amount_minor);
        $this->assertDatabaseCount('orders',2);
    }

    public function test_enabled_payments_allow_only_the_bank_form_in_sandbox(): void {
        $this->get('/')->assertHeader('Content-Security-Policy', "form-action 'self' https://www.cpay.com.mk; frame-src 'none'");
    }
}
