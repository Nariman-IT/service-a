<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\AdminDiscountController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Product\ProductController;



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


