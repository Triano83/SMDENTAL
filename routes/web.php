<?php

use App\Http\Controllers\AlbaranController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::apiResource('clientes', ClienteController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('albaranes', AlbaranController::class);
Route::apiResource('facturas', FacturaController::class);
Route::post('facturas/generar-mensual', [FacturaController::class, 'generarFacturasMensuales']);
