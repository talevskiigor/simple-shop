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
});
