<?php
namespace App\Services\Payments;
use App\Models\{Order, PaymentAttempt};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Cpay
{
    public function enabled(): bool { return (bool) config('payments.enabled'); }
    public function testMode(): bool {
        $value = config('payments.test_amount_mkd');
        if ($value === null || $value === '') return false;
        if ((string) $value !== '1') throw new \RuntimeException('PAYMENT_TEST_AMOUNT_MKD must be 1 or unset.');
        return true;
    }
    public function prepare(Order $order): PaymentAttempt {
        abort_unless($this->enabled(), 503, 'Payments are disabled in this test environment.');
        foreach (['merchant_id','merchant_name','secret'] as $key) if (!config('payments.'.$key)) throw new \RuntimeException('Payment configuration is incomplete.');
        return DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);
            abort_if($order->finished, 409, 'This order is already complete.');
            $existing = PaymentAttempt::where('order_id', $order->id)->latest('id')->first();
            if ($existing && $existing->status !== 'failed') return $existing;
            $test = $this->testMode();
            $amount = $test ? 100 : (int) round((float) $order->total * 100);
            // The merchant protocol accepts whole MKD only. Never silently alter a normal total.
            if ($amount < 100 || $amount % 100 !== 0) throw ValidationException::withMessages(['payment' => 'Payment total must be at least one whole denar.']);
            $attempt = new PaymentAttempt(['order_id' => $order->id, 'reference' => strtoupper(bin2hex(random_bytes(5))), 'return_token' => bin2hex(random_bytes(32)), 'amount_minor' => $amount, 'is_test' => $test]);
            $fields = [
                'AmountToPay' => (string) $amount,
                'PayToMerchant' => (string) config('payments.merchant_id'),
                'MerchantName' => (string) config('payments.merchant_name'),
                'AmountCurrency' => 'MKD',
                'Details1' => ($test ? 'Test ' : 'Order ').$order->id,
                'Details2' => $attempt->reference,
                'PaymentOKURL' => url('/bank/ok').'?token='.$attempt->return_token,
                'PaymentFailURL' => url('/bank/fail').'?token='.$attempt->return_token,
                'FirstName' => mb_substr(Str::ascii($order->first), 0, 64),
                'LastName' => mb_substr(Str::ascii($order->last), 0, 64),
                'Address' => mb_substr(Str::ascii($order->address), 0, 50),
                'City' => mb_substr(Str::ascii($order->city), 0, 50),
                'Country' => '807',
                'Telephone' => preg_replace('/^0/', '389', $order->phone),
                'Email' => $order->email,
            ];
            if (strlen($order->email) > 64) throw ValidationException::withMessages(['email' => 'The payment provider allows email addresses up to 64 characters.']);
            $attempt->request_fields = array_filter($fields, fn ($v) => $v !== '');
            $attempt->save();
            return $attempt;
        });
    }
    public function form(PaymentAttempt $attempt): array {
        $signature = app(CpayChecksum::class)->sign($attempt->request_fields, (string) config('payments.secret'));
        return [...$attempt->request_fields, 'CheckSumHeader' => $signature['header'], 'CheckSum' => $signature['checksum']];
    }
    public function receive(array $input, string $token, bool $success): PaymentAttempt {
        abort_unless($this->enabled(), 404);
        $fields = app(CpayChecksum::class)->verify($input, (string) config('payments.secret'));
        return DB::transaction(function () use ($fields, $input, $token, $success) {
            $attempt = PaymentAttempt::where('reference', $fields['Details2'] ?? '')->lockForUpdate()->firstOrFail();
            abort_unless(hash_equals($attempt->return_token, $token), 422, 'Invalid bank response.');
            foreach ($attempt->request_fields as $key => $value) abort_unless(isset($fields[$key]) && hash_equals((string) $value, $fields[$key]), 422, 'Payment details do not match.');
            $signed = array_keys($fields); $original = array_keys($attempt->request_fields);
            // Reject reflecting the outgoing checksum back as a bank response.
            abort_unless(($signed[0] ?? null) === $original[1] && ($signed[1] ?? null) === $original[0], 422, 'Invalid return parameter order.');
            $ref = $fields['cPayPaymentRef'] ?? null;
            if ($success) abort_unless(is_string($ref) && preg_match('/^[0-9]{1,20}$/D', $ref), 422, 'Missing bank reference.');
            if ($ref) abort_if(PaymentAttempt::where('bank_reference', $ref)->whereKeyNot($attempt->id)->exists(), 409, 'Bank reference already recorded.');
            if (in_array($attempt->status, ['returned_success', 'confirmed'], true)) return $attempt;
            // v2.9 signs the echoed fields, not an independent success status. Keep
            // this as a bank-return receipt pending merchant-portal reconciliation;
            // do not fulfill orders or decrement stock based on a browser URL.
            $attempt->status = $success ? 'returned_success' : 'failed';
            $attempt->bank_reference = $ref;
            $attempt->save();
            return $attempt;
        });
    }
}
