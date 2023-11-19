<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['middleware' => ['web']], function () {
    Route::resource('/', \App\Http\Controllers\HomeController::class);
    Route::resource('/product', \App\Http\Controllers\ProductController::class);
    Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('/cart', \App\Http\Controllers\CartController::class);
    Route::resource('/order', \App\Http\Controllers\OrderController::class);
    Route::resource('/search', \App\Http\Controllers\SearchController::class);

    Route::resource('/pages', \App\Http\Controllers\PageController::class);
    Route::resource('/contact', \App\Http\Controllers\ContactController::class);

    Route::get('login', function () {
        return redirect(route('dashboard'));
    });
    Route::get('/media-resize/{slug?}', function (?string $slug = null) {

        $w = request('w', 'null');
        $h = request('h', 'null');
        $q = request('q', 100);

        $file = public_path('media/' . $slug);
        $newExtension = 'webp';

        $name = basename($file, '.' . explode('.', $file)[1],);
        $newFile = sprintf('%s/%s-%sX_%s_%s.%s', dirname($file), $name, $w, $h, $q, $newExtension);
        $newFile = str_replace('/var/www/html/public/media', '/var/www/html/public/cached-media', $newFile);
        if (File::exists($newFile)) {
            $img = Image::make($newFile);
        } else {
            $d = dirname($newFile);
            if (!File::isDirectory($d)) {

                File::makeDirectory($d, 777, true);
            }
//            dd($d);
            $img = Image::make($file)->resize($w, $h, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($newFile, 50, $newExtension);

        }
        return $img->response();
    })->where('slug', '.*');

    Route::post('/bank/ok', [\App\Http\Controllers\BankController::class, 'ok']);
    Route::post('/bank/fail', [\App\Http\Controllers\BankController::class, 'fail']);

});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->prefix('admin')->name('dashboard');

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('pages', PageController::class);
});

require __DIR__ . '/auth.php';
