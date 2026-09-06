<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogoController;

Route::get('/', [HomeController::class, 'index']);
//Catalogos de proveedores
Route::post('/proveedores/catalogos', [CatalogoController::class, 'store']);
Route::delete('/proveedores/catalogos/{id}', [CatalogoController::class, 'destroy']);


//PROVEEDORES
Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
Route::post('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
Route::post('/proveedores/{id}/restore', [ProveedorController::class, 'restore'])->name('proveedores.restore');
Route::get('/proveedor/{id}', [ProveedorController::class, 'show'])->name('proveedores.show');

