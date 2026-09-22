<?php

use Illuminate\Support\Facades\Route;

// ── Controladores tuyos y de tu compañero ──
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\MovimientoBodegaController;
use App\Http\Controllers\AtributoController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VarianteController;

// ── Autenticación ────────────────────────────────────────────

use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CatalogoController;

// ── Autenticación (Rutas públicas) ───────────────────────────

Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Recuperación de Contraseña (Rutas públicas) ─────────────
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/preview-reset-email', [AuthController::class, 'previewResetEmail'])->name('password.preview');
Route::get('/preview-reset-password', [AuthController::class, 'previewResetPasswordView'])->name('password.preview.view');

// ── Rutas protegidas (requieren estar logueado) ─────────────
Route::middleware('auth')->group(function () {
    
    // Inicio
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home.alias');



    // ── Perfil del usuario autenticado ─────────────────────────────
    Route::get('/perfil',          [ProfileController::class, 'show'])->name('perfil.show');
    Route::put('/perfil',          [ProfileController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');

    // Rutas de proveedores movidas a middleware admin

    // ── Módulo de Gestión de Usuarios (CRUD) - Solo Administradores ──
    Route::middleware('admin')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
        Route::patch('/usuarios/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('usuarios.toggleStatus');

        // ── Módulo de Categorías ──
        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
        Route::get('/categorias/export', [CategoriaController::class, 'exportPdf'])->name('categorias.export');
        Route::post('/categorias/reorder', [CategoriaController::class, 'reorder'])->name('categorias.reorder');
        Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::patch('/categorias/{categoria}/toggle-status', [CategoriaController::class, 'toggleStatus'])->name('categorias.toggleStatus');


        Route::get('/compras/nueva', [CompraController::class, 'create'])->name('compras.create');
        Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');
        Route::get('/compras/historial', [CompraController::class, 'historial'])->name('compras.historial');
        Route::get('/compras/movimientos', [CompraController::class, 'movimientos'])->name('compras.movimientos');
        Route::post('/movimientos-bodega/transferencia-tienda', [MovimientoBodegaController::class, 'transferirATienda'])->name('movimientos-bodega.transferencia-tienda');

        // ── Módulo de Atributos ──
        Route::resource('atributos', AtributoController::class)->except(['create', 'show', 'edit']);

        // ── TU MÓDULO DE PROVEEDORES Y CATÁLOGOS ──
        
        // 1. Estáticas (Catálogos)
        Route::post('/proveedores/catalogos', [CatalogoController::class, 'store']);
        Route::delete('/proveedores/catalogos/{id}', [CatalogoController::class, 'destroy']);

        // 2. Dinámicas (Proveedores)
        Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::post('/proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('/proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
        Route::post('/proveedores/{id}/restore', [ProveedorController::class, 'restore'])->name('proveedores.restore');
        Route::get('/proveedor/{id}', [ProveedorController::class, 'show'])->name('proveedores.show');

        // ── Productos: CRUD solo Admin ──
        Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

        // ── Variantes AJAX: solo Admin ──
        Route::post('/productos/{producto}/variantes', [VarianteController::class, 'store'])->name('variantes.store');
        Route::get('/variantes/{id}', [VarianteController::class, 'show'])->name('variantes.show');
        Route::put('/variantes/{id}', [VarianteController::class, 'update'])->name('variantes.update');
        Route::delete('/variantes/{id}', [VarianteController::class, 'destroy'])->name('variantes.destroy');
    });

    // ── Productos: índice visible para todos los usuarios autenticados ──
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
});
