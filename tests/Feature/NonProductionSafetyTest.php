<?php

namespace Tests\Feature;

use App\Classes\CaSys;
use App\Http\Middleware\NonProductionSafety;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class NonProductionSafetyTest extends TestCase
{
    public function test_legacy_import_is_blocked_without_touching_a_database(): void
    {
        $this->get('/update')->assertNotFound()->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
    }

    public function test_both_payment_callbacks_are_blocked(): void
    {
        $this->post('/bank/ok', ['Details2' => 1])->assertNotFound();
        $this->post('/bank/fail', ['Details2' => 1])->assertNotFound();
    }

    public function test_public_staff_registration_is_blocked(): void
    {
        $this->get('/admin/register')->assertNotFound();
        $this->post('/admin/register', [])->assertNotFound();
    }

    public function test_payment_fields_cannot_be_generated_in_a_sandbox(): void
    {
        $this->expectException(HttpException::class);
        CaSys::get(new Order());
    }

    public function test_allowed_sandbox_responses_prevent_external_form_submission(): void
    {
        $response = (new NonProductionSafety())->handle(Request::create('/cart'), fn () => response('Cart'));

        $this->assertSame("form-action 'self'; frame-src 'none'", $response->headers->get('Content-Security-Policy'));
        $this->assertSame('Cart', $response->getContent());
    }

    public function test_production_preserves_private_indexing_and_form_safety(): void
    {
        config(['store.sandbox' => false, 'store.allow_indexing' => true]);
        $response = (new NonProductionSafety())->handle(Request::create('/'), fn () => response('Passed through'));

        $this->assertSame('Passed through', $response->getContent());
        $this->assertSame("form-action 'self'; frame-src 'none'", $response->headers->get('Content-Security-Policy'));
        $this->assertFalse($response->headers->has('X-Robots-Tag'));

        $private = (new NonProductionSafety())->handle(Request::create('/admin/login'), fn () => response('Private'));
        $this->assertSame('noindex, nofollow, noarchive', $private->headers->get('X-Robots-Tag'));
        $this->get('/update')->assertNotFound();
        $this->post('/bank/ok')->assertNotFound();
    }
}
