<?php

use App\Http\Controllers\AlbaranController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;



// Rutas de Vistas
Route::get('/', function () {
    return view('welcome');
});

// Rutas para Clientes
Route::get('/clientes', [ClienteController::class, 'indexWeb'])->name('clientes.index');
Route::get('/clientes/create', [ClienteController::class, 'createWeb'])->name('clientes.create');

// Rutas para Productos
Route::get('/productos', [ProductoController::class, 'indexWeb'])->name('productos.index');
Route::get('/productos/create', [ProductoController::class, 'createWeb'])->name('productos.create');

// Rutas para Albaranes (solo listado por ahora, creación más avanzada)
Route::get('/albaranes', [AlbaranController::class, 'indexWeb'])->name('albaranes.index');
Route::get('/albaranes/create', [AlbaranController::class, 'createWeb'])->name('albaranes.create'); // ¡Nueva ruta!

// Rutas para Facturas (solo listado por ahora, creación más avanzada)
Route::get('/facturas', [FacturaController::class, 'indexWeb'])->name('facturas.index');
Route::get('/facturas/create', [FacturaController::class, 'createWeb'])->name('facturas.create'); // ¡Nueva ruta!


// Rutas de API (tus apiResource existentes)
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('albaranes', AlbaranController::class);
Route::apiResource('facturas', FacturaController::class);

// Ruta para generar facturas mensuales (POST, ya la tenías)
Route::post('facturas/generar-mensual', [FacturaController::class, 'generarFacturasMensuales']);