<?php

namespace App\Classes;

use App\Models\Order;
use Carbon\Carbon;

class CaSys
{

    public static function get(Order $order): array
    {
        // $pass = 'TEST_PASS';

        $pass = 'SmZfYVa9vK2wwFCU4hJSZQ6mTBMRthBA';

        $price = (int)($order->total * 100);
//        $price = 100;

        $items = [
            'AmountToPay' => $price,
            'PayToMerchant' => '1000002188',
            'MerchantName' => 'For Kids (mk)',
            'AmountCurrency' => 'MKD',
            'Details1' =>'Нарачка бр.: '. Carbon::now()->year  . Carbon::now()->month . Carbon::now()->day .'-' . $order->id,
            'Details2' => $order->id,
            'PaymentOKURL' => url('bank/ok'),
            'PaymentFailURL' => url('bank/fail'),

            'FirstName' => $order->first,
            'LastName' => $order->last,
            'Address' => $order->address,
            'City' => $order->city,
//            'Zip' => '1000',
            'Country' => 'Macedonia',
            'Telephone' => $order->phone,
            'Email' => $order->email,
//            'OriginalAmount' => '150',
//            'OriginalCurrency' => 'EUR',
        ];

        $sizes = '';
        foreach ($items as $key => $value) {
            $sizes .= str_pad(mb_strlen($value), 3, '0', STR_PAD_LEFT);
        }

        $header = count($items) . implode(',', array_keys($items)) . ',' . $sizes . implode('', array_values($items)) . $pass;

        $CheckSum = strtoupper(md5($header));

        $CheckSumHeader = count($items) . implode(',', array_keys($items)) . ',' . $sizes;

        $items['CheckSumHeader'] = $CheckSumHeader;
        $items['CheckSum'] = $CheckSum;
        return $items;
    }

}
