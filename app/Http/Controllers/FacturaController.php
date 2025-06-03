<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Factura $factura)
    {
        //
    }

    public function edit(Factura $factura)
    {
        //
    }

    public function update(Request $request, Factura $factura)
    {
        //
    }

    public function destroy(Factura $factura)
    {
        //
    }

    public function generarFacturasMensuales()
    {
        $clientes = Cliente::with(['albaranes' => function ($query) {
            $query->whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year);
        }])->get();

        foreach ($clientes as $cliente) {
            $total = 0;
            $factura = Factura::create([
                'fecha' => now(),
                'cliente_id' => $cliente->id,
                'total' => 0
            ]);

            foreach ($cliente->albaranes as $albaran) {
                $importe = $albaran->productos->sum(function ($prod) {
                    return $prod->pivot->importe_total;
                });

                $factura->albaranes()->attach($albaran->id, ['importe' => $importe]);
                $total += $importe;
            }

            $factura->update(['total' => $total]);
        }

        return response()->json(['status' => 'Facturas generadas']);
    }
}
