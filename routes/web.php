<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::resource('products', ProductController::class)
    ->only(['create', 'store']);

Route::resource('categories', CategoryController::class)
    ->only(['create', 'store']);
