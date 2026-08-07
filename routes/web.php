<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/variante', [HomeController::class, 'variant']);
Route::get('/variante-premium', [HomeController::class, 'variantPremium']);
Route::get('/variante-apple', [HomeController::class, 'variantApple']);
Route::get('/productos', [HomeController::class, 'inventario']);

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
