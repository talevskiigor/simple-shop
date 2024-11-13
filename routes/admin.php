<?php

use App\Http\Controllers\ProfileController;

Route::group(['prefix' => 'admin', 'middleware' => ['auth','verified']], function () {



    Route::resource('/', \App\Http\Controllers\HomeController::class);
    Route::resource('/media', \App\Http\Controllers\MediaController::class);

//    Route::get('/dashboard', function () {
//        return view('dashboard');
//    });
    //->middleware(['auth', 'verified'])->prefix('admin')->name('dashboard');

    Route::resource('/product', \App\Http\Controllers\Admin\ProductController::class);

    Route::resource('/orders', \App\Http\Controllers\Admin\OrdersController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::resource('/categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('/search', \App\Http\Controllers\SearchController::class);
    Route::resource('/pages', \App\Http\Controllers\PageController::class);
});
