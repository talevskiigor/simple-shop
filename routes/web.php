<?php

use App\Models\{Category, Product, Page};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

Route::get('/', fn () => view('home', ['items' => Product::where('active', true)->where('quantity', '>', 0)->get()]));
Route::get('/categories/{slug}', function (string $slug) {
    $category = Category::where('slug', $slug)->firstOrFail();
    return view('home', ['items' => $category->product()->where('active', true)->where('quantity', '>', 0)->get()]);
});
Route::get('/product/{slug}', function (string $slug) {
    return view('product', ['item' => Product::where('active', true)->where('slug', $slug)->with(['media', 'category'])->firstOrFail()]);
})->name('product.show');
Route::get('/search/suggest', [\App\Http\Controllers\SearchController::class, 'suggest'])->middleware('throttle:120,1');
Route::get('/search', function (Request $request) {
    $data = $request->validate(['find' => 'nullable|string|max:200']);
    return view('home', ['items' => Product::search(Str::ascii(trim($data['find'] ?? '')))->query(fn ($query) => $query->where('active', true))->get()]);
});
Route::get('/pages/{slug}', fn (string $slug) => view('helpers.page', ['page' => Page::where('published', true)->where('slug', $slug)->firstOrFail()]));
Route::resource('order', \App\Http\Controllers\OrderController::class)->only(['index', 'store']);
Route::resource('cart', \App\Http\Controllers\CartController::class)->only(['index', 'store', 'destroy']);
Route::resource('contact', \App\Http\Controllers\ContactController::class)->only(['index', 'store']);

// Preserve the previously published logo URLs while storing each original once.
Route::get('/logo/{file}', function (string $file) {
    // Dots are part of the filename, not configuration nesting.
    $target = config('legacy_media')[$file] ?? null;
    abort_unless($target, 404);
    return redirect(\App\Helpers\Image::get($target[0], $target[1]));
});
Route::get('/media-resize/{slug}', fn (Request $request, string $slug) => app(\App\Services\ImageVariants::class)->response($request, $slug))->where('slug', '.*')->middleware('throttle:240,1');
Route::get('/media/{slug}', function (string $slug) {
    $file = app(\App\Services\MediaFiles::class)->resolve($slug);
    abort_unless($file, 404);
    $mime = mime_content_type($file);
    abort_unless(in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/x-icon', 'image/vnd.microsoft.icon', 'video/mp4', 'video/webm']), 404);
    return response()->file($file, ['Content-Type' => $mime, 'X-Content-Type-Options' => 'nosniff']);
})->where('slug', '.*');
Route::post('/bank/ok', [\App\Http\Controllers\BankController::class, 'ok']);
Route::post('/bank/fail', [\App\Http\Controllers\BankController::class, 'fail']);
Route::get('/payment/result/{token}', [\App\Http\Controllers\BankController::class, 'result'])->where('token', '[a-f0-9]{64}');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
