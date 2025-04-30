<?php

use App\Http\Controllers\Api\Admin\AdminDiscountController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Cart\CartController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\AdminController;





Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::post('/order', [OrderController::class, 'create'])->name('order.create');

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('auth.login');
    Route::post('/register', 'register')->name('auth.register');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/current/order', 'currentOrder')->name('order.current');
        Route::get('/history', 'historyOrder')->name('order.history');
    });
});


Route::controller(ProductController::class)->group(function () {
    Route::get('/product',  'product')->name('index.product');
    Route::post('product/categories', 'categories')->name('categories.product');
    Route::post('product/search', 'search')->name('search.product');
});


Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::controller(AdminController::class)->group(function () {
        Route::post('/admin/store', 'store')->name('admin.store');
        Route::patch('/admin/update', 'update')->name('admin.update');
        Route::delete('/admin/delete', 'delete')->name('admin.delete');
    });
    Route::controller(AdminOrderController::class)->group(function () {
        Route::get('/admin/list', 'listOrder')->name('admin.list');
        Route::patch('/admin/order/update', 'orderUpdate')->name('admin.updateOrder');
    });
    Route::controller(AdminDiscountController::class)->group(function () {
        Route::patch('admin/discount/set', 'setDiscount')->name('admin.set');
        Route::patch('admin/discount/remove', 'removeDiscount')->name('admin.remove');
    });
});


