<?php

// Read-only inventory: never print customer records, credentials, or provider payloads.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

if (!config('store.sandbox') || !in_array(DB::connection()->getDatabaseName(), ['local_forkids', 'stg_forkids'], true)) {
    throw new RuntimeException('Inventory is restricted to the restored sandbox databases.');
}

$counts = [];
foreach (['products', 'categories', 'media', 'pages', 'orders', 'users'] as $table) {
    $counts[$table] = DB::table($table)->count();
}
$missing = [];
$used = [];
$check = function ($kind, $id, $quantity, $path) use (&$missing, &$used) {
    $used[$path] = true;
    if (!is_file(public_path('media/'.$path))) {
        $missing[] = ['kind' => $kind, 'product' => $id, 'in_stock' => $quantity > 0, 'path' => $path];
    }
};
foreach (DB::table('products')->get(['id', 'quantity', 'image']) as $product) {
    $check('cover', $product->id, $product->quantity, $product->image);
}
foreach (DB::table('media_product')
    ->join('products', 'products.id', '=', 'media_product.product_id')
    ->join('media', 'media.id', '=', 'media_product.media_id')
    ->get(['products.id', 'products.quantity', 'media.path']) as $product) {
    $check('gallery', $product->id, $product->quantity, $product->path);
}
$unexpectedOrders = DB::table('orders')->where(function ($query) {
    $query->where('first', '!=', 'Test')->orWhereNotNull('comment')
        ->orWhere('email', 'not like', '%@example.test')
        ->orWhere('address', '!=', 'Test address')->orWhere('city', '!=', 'Test city')
        ->orWhere('phone', '!=', '070000000');
})->count();

echo json_encode([
    'php' => PHP_VERSION,
    'database_version' => DB::selectOne('SELECT VERSION() AS v')->v,
    'counts' => $counts,
    'non_anonymized_orders' => $unexpectedOrders,
    'unique_referenced_media' => count($used),
    'missing_references' => $missing,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL;
