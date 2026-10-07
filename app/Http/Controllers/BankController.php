<?php
namespace App\Http\Controllers;
use App\Models\PaymentAttempt;
use App\Services\Payments\Cpay;
use Illuminate\Http\Request;
class BankController extends Controller {
    public function ok(Request $request) { return $this->receive($request, true); }
    public function fail(Request $request) { return $this->receive($request, false); }
    private function receive(Request $request, bool $success) {
        // Use untrimmed POST values: byte/character fidelity is required for the signature.
        $raw = []; parse_str($request->getContent(), $raw);
        if (!$raw && app()->environment('testing')) $raw = $request->post();
        $attempt = app(Cpay::class)->receive($raw, (string) $request->query('token'), $success);
        return redirect('/payment/result/'.$attempt->return_token, 303);
    }
    public function result(string $token) {
        $attempt = PaymentAttempt::where('return_token', $token)->firstOrFail();
        return response()->view('bank.result', compact('attempt'))->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }
}
