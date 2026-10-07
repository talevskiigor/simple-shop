<?php
return [
    'enabled' => (bool) env('PAYMENTS_ENABLED', false),
    // Leave unset for normal totals. The only accepted override is exactly 1 MKD.
    'test_amount_mkd' => env('PAYMENT_TEST_AMOUNT_MKD'),
    'merchant_id' => env('CPAY_MERCHANT_ID'),
    'merchant_name' => env('CPAY_MERCHANT_NAME'),
    'secret' => env('CPAY_SECRET'),
    'url' => 'https://www.cpay.com.mk/client/Page/default.aspx?xml_id=/mk-MK/.loginToPay/.simple/',
];
