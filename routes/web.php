<?php

use App\Http\Controllers\AlbaranController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;



// Rutas de Vistas (estas son las que sirven las páginas HTML)
Route::get('/', function () {
    return view('welcome');
});

Route::get('/clientes', [ClienteController::class, 'indexWeb'])->name('clientes.index');
Route::get('/clientes/create', [ClienteController::class, 'createWeb'])->name('clientes.create');

Route::get('/productos', [ProductoController::class, 'indexWeb'])->name('productos.index');
Route::get('/productos/create', [ProductoController::class, 'createWeb'])->name('productos.create');

Route::get('/albaranes', [AlbaranController::class, 'indexWeb'])->name('albaranes.index');
Route::get('/albaranes/create', [AlbaranController::class, 'createWeb'])->name('albaranes.create');

Route::get('/facturas', [FacturaController::class, 'indexWeb'])->name('facturas.index');
Route::get('/facturas/create', [FacturaController::class, 'createWeb'])->name('facturas.create');


// --- RUTAS DE API ---
// Estas rutas apiResource se agrupan bajo el prefijo 'api'.
// Esto significa que responderán a URLs como /api/clientes, /api/productos, etc.
Route::prefix('api')->group(function () {
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('albaranes', AlbaranController::class);
    Route::apiResource('facturas', FacturaController::class);
});

// Ruta personalizada para generar facturas mensuales (sigue siendo una ruta web POST)
// No la metemos en el grupo 'api' porque el botón de la navbar ya apunta a '/facturas/generar-mensual'
Route::post('facturas/generar-mensual', [FacturaController::class, 'generarFacturasMensuales']);
