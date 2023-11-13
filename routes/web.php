<?php

use Illuminate\Support\Facades\Route;

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
    Route::resource('/search',\App\Http\Controllers\SearchController::class);

    Route::get('/media-resize/{slug?}', function(?string $slug = null) {


        $w = request('w', null);
        $h = request('h',null);
        $q = request('q',100);

        $file = public_path($slug);
        $newExtension='webp';
        $name = basename($file, '.'. explode('.',$file)[1],);
        $newFile = sprintf('%s/%s-%sX_%s_%s.%s', dirname( $file),$name,$w,$h,$q,$newExtension);

        if(File::exists($newFile)){
            $img = Image::make($newFile);
        }else {
            $img = Image::make($file)->resize($w, $h,function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($newFile,50,$newExtension);

        }
        return $img->response();
    })->where('slug', '.*');

    Route::post('/bank/ok', [\App\Http\Controllers\BankController::class,'ok']);
    Route::post('/bank/fail',[\App\Http\Controllers\BankController::class,'fail']);

});


