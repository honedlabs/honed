<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('products', ProductController::class);

Route::get('/products/{product}/delete', [ProductController::class, 'delete'])
    ->name('products.delete');
