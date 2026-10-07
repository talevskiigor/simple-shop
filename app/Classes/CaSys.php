<?php
namespace App\Classes;
use App\Models\Order;
use App\Services\Payments\Cpay;
/** Compatibility entry point for the existing hosted payment form. */
class CaSys {
    public static function get(Order $order): array {
        $gateway = app(Cpay::class);
        return $gateway->form($gateway->prepare($order));
    }
}
