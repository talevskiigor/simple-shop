<?php

// Container variables also populate $_SERVER, which precedes getenv() in Dotenv.
// Force every source before Laravel boots; never let RefreshDatabase select the restored copy.
$settings = [
    'APP_ENV' => 'testing',
    'APP_CONFIG_CACHE' => sys_get_temp_dir().'/forkids-tests-'.getmypid().'-config.php',
    'APP_KEY' => 'base64:'.base64_encode(str_repeat('t', 32)),
    'APP_DEBUG' => 'false',
    'STORE_SANDBOX' => 'true',
    'STORE_ALLOW_INDEXING' => 'false',
    'PAYMENTS_ENABLED' => 'false',
    'PAYMENT_TEST_AMOUNT_MKD' => '',
    'CPAY_SECRET' => '',
    'CPAY_MERCHANT_ID' => '',
    'CPAY_MERCHANT_NAME' => '',
    'BACKUPS_ENABLED' => 'false',
    'MAIL_COPY_TO' => '',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => ':memory:',
    'DATABASE_URL' => '',
    'DB_URL' => '',
    'SCOUT_DRIVER' => 'null',
    'MAIL_MAILER' => 'array',
    'CACHE_DRIVER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'SESSION_DRIVER' => 'array',
];
foreach ($settings as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $_SERVER[$key] = $value;
}
require __DIR__.'/../vendor/autoload.php';
