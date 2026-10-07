<?php

use App\Http\Controllers\Admin\{AdministratorController, CategoryController, MediaLibraryController, PageController, ProductController, OrdersController};
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth', 'admin', 'auth.session'])->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
    Route::resource('product', ProductController::class)->except('show');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('pages', PageController::class)->except('show');
    Route::resource('media', MediaLibraryController::class)->parameters(['media' => 'media'])->only(['index', 'store', 'update', 'destroy']);
    Route::resource('users', AdministratorController::class)->except('show');
    Route::get('orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
