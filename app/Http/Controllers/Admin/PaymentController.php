<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{PaymentAttempt, Order, Product};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller {
    public function confirm(Request $request, PaymentAttempt $attempt) {
        $request->validate(['bank_verified' => 'accepted']);
        DB::transaction(function () use ($attempt, $request) {
            $attempt = PaymentAttempt::lockForUpdate()->findOrFail($attempt->id);
            if ($attempt->status === 'confirmed') return;
            abort_unless($attempt->status === 'returned_success' && $attempt->bank_reference, 422, 'No successful bank return to reconcile.');
            $order = Order::lockForUpdate()->findOrFail($attempt->order_id);
            if (!$attempt->is_test) {
                abort_if($order->finished, 409, 'Order already completed; reconcile a possible duplicate charge.');
                $items = json_decode($order->items, true, flags: JSON_THROW_ON_ERROR);
                $ids = array_column($items, 'id');
                $products = Product::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
                foreach ($items as $item) {
                    $product = $products->get($item['id']);
                    abort_unless($product && $product->quantity >= ($item['quantity'] ?? 1), 409, 'Paid order requires manual stock reconciliation. No stock was changed.');
                    $product->decrement('quantity', $item['quantity'] ?? 1);
                }
                $order->forceFill(['finished' => true, 'bank_ref' => $attempt->bank_reference])->save();
            }
            $attempt->forceFill(['status' => 'confirmed', 'confirmed_at' => now(), 'confirmed_by' => $request->user()->id])->save();
        });
        return redirect('/admin/orders')->with('status', 'Payment confirmation recorded. Test orders do not change stock.');
    }
}
