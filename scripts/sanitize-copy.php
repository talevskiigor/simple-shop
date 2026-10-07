<?php

// Run only after restoring into a new isolated database. Read the new admin password on stdin.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$database = DB::connection()->getDatabaseName();
if ($app->isProduction() || !config('store.sandbox') || !in_array($database, ['local_forkids', 'stg_forkids'], true)) {
    fwrite(STDERR, "Refusing to sanitize a database outside the sandbox allowlist.\n");
    exit(1);
}
$password = trim(stream_get_contents(STDIN));
if (strlen($password) < 20) {
    fwrite(STDERR, "Supply a fresh admin password of at least 20 characters on stdin.\n");
    exit(1);
}

$counts = [];
foreach (['products', 'categories', 'media', 'pages', 'orders'] as $table) {
    $counts[$table] = DB::table($table)->count();
}

DB::transaction(function () use ($password) {
    foreach (['sessions', 'password_reset_tokens', 'personal_access_tokens', 'jobs', 'failed_jobs'] as $table) {
        DB::table($table)->delete();
    }
    DB::table('orders')->update([
        'first' => 'Test', 'last' => DB::raw("CONCAT('Customer ', id)"),
        'address' => 'Test address', 'city' => 'Test city', 'phone' => '070000000',
        'email' => DB::raw("CONCAT('order-', id, '@example.test')"), 'comment' => null,
        'bank_ref' => DB::raw("CASE WHEN bank_ref IS NULL OR bank_ref = '' THEN NULL ELSE CONCAT('TEST-', id) END"),
    ]);
    DB::table('users')->update([
        'name' => DB::raw("CONCAT('Test User ', id)"),
        'email' => DB::raw("CONCAT('user-', id, '@example.test')"),
        'password' => Hash::make(bin2hex(random_bytes(32))), 'remember_token' => null,
    ]);
    DB::table('users')->updateOrInsert(['id' => 1], [
        'name' => 'Test Administrator', 'email' => 'admin@forkids.test',
        'password' => Hash::make($password), 'email_verified_at' => now(),
        'remember_token' => null, 'created_at' => now(), 'updated_at' => now(),
    ]);
});

foreach ($counts as $table => $count) {
    if (DB::table($table)->count() !== $count) {
        throw new RuntimeException('Unexpected record count change: '.$table);
    }
}
echo json_encode(['database' => $database, 'anonymized' => true, 'counts' => $counts], JSON_PRETTY_PRINT).PHP_EOL;
