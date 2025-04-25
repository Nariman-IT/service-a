<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;


Route::get('/assortment', [HomeController::class, 'assortment'])->name('home.assortment');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/order', [OrderController::class, 'create'])->name('order.create');
Route::middleware('auth:sanctum')->get('/myOrder', [HomeController::class, 'myOrder'])->name('order.myOrder');
Route::middleware('auth:sanctum')->get('/history', [OrderController::class, 'history'])->name('order.history');
Route::middleware('auth:sanctum')->post('/admin/store', [AdminController::class, 'store'])->name('admin.store');
Route::middleware('auth:sanctum')->patch('/admin/update', [AdminController::class, 'update'])->name('admin.update');
Route::middleware('auth:sanctum')->delete('/admin/delete', [AdminController::class, 'delete'])->name('admin.delete');
Route::middleware('auth:sanctum')->get('/admin/list', [AdminController::class, 'list'])->name('admin.list');
Route::middleware('auth:sanctum')->patch('/admin/order', [AdminController::class, 'order'])->name('admin.order');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

