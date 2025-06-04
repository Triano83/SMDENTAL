<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Albaran; // Necesitamos el modelo Albaran
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class FacturaController extends Controller
{
    public function index()
    {
        // Obtener todas las facturas con sus clientes y albaranes asociados
        $facturas = Factura::with('cliente', 'albaranes.productos')->get();
        return response()->json($facturas);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada para la factura
            $validatedData = $request->validate([
                'fecha' => 'required|date',
                'cliente_id' => 'required|exists:clientes,id',
                'albaranes_ids' => 'required|array|min:1', // IDs de albaranes a incluir en la factura
                'albaranes_ids.*' => 'required|exists:albarans,id', // Cada ID de albarán debe existir
            ]);

            // Verificar que todos los albaranes pertenecen al mismo cliente
            $albaranes = Albaran::whereIn('id', $validatedData['albaranes_ids'])
                                ->where('cliente_id', $validatedData['cliente_id'])
                                ->with('productos') // Cargar productos para calcular el total del albarán
                                ->get();

            if ($albaranes->count() !== count($validatedData['albaranes_ids'])) {
                return response()->json(['message' => 'Algunos albaranes no pertenecen al cliente especificado o no existen.'], 400);
            }

            $totalFactura = 0;
            $albaranesToAttach = [];

            foreach ($albaranes as $albaran) {
                // Calcular el importe total de este albarán sumando los importes de sus productos
                $importeAlbaran = $albaran->productos->sum(function ($prod) {
                    return $prod->pivot->importe_total;
                });
                $totalFactura += $importeAlbaran;

                $albaranesToAttach[$albaran->id] = ['importe' => $importeAlbaran];
            }

            // Crear la factura
            $factura = Factura::create([
                'fecha' => $validatedData['fecha'],
                'cliente_id' => $validatedData['cliente_id'],
                'total' => $totalFactura, // El total se calcula en base a los albaranes
            ]);

            // Adjuntar los albaranes a la factura con sus importes
            $factura->albaranes()->attach($albaranesToAttach);

            // Cargar relaciones para la respuesta
            $factura->load('cliente', 'albaranes.productos');

            return response()->json($factura, 201); // 201 Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear la factura: ' . $e->getMessage()], 500);
        }
    }

    public function show(Factura $factura)
    {
        // Cargar las relaciones para mostrar el detalle completo
        $factura->load('cliente', 'albaranes.productos');
        return response()->json($factura);
    }

    public function edit(Factura $factura)
    {
        //
    }

    public function update(Request $request, Factura $factura)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'fecha' => 'sometimes|required|date',
                'cliente_id' => 'sometimes|required|exists:clientes,id',
                'albaranes_ids' => 'sometimes|array|min:1',
                'albaranes_ids.*' => 'required_with:albaranes_ids|exists:albarans,id',
            ]);

            // Actualizar campos de la factura
            $factura->update([
                'fecha' => $validatedData['fecha'] ?? $factura->fecha,
                'cliente_id' => $validatedData['cliente_id'] ?? $factura->cliente_id,
            ]);

            // Si se envían albaranes para actualizar la relación
            if (isset($validatedData['albaranes_ids'])) {
                $albaranes = Albaran::whereIn('id', $validatedData['albaranes_ids'])
                                    ->where('cliente_id', $factura->cliente_id) // Asegurar que pertenecen al mismo cliente
                                    ->with('productos')
                                    ->get();

                if ($albaranes->count() !== count($validatedData['albaranes_ids'])) {
                    return response()->json(['message' => 'Algunos albaranes no pertenecen al cliente actual de la factura o no existen.'], 400);
                }

                $totalFactura = 0;
                $albaranesToSync = [];

                foreach ($albaranes as $albaran) {
                    $importeAlbaran = $albaran->productos->sum(function ($prod) {
                        return $prod->pivot->importe_total;
                    });
                    $totalFactura += $importeAlbaran;
                    $albaranesToSync[$albaran->id] = ['importe' => $importeAlbaran];
                }

                $factura->albaranes()->sync($albaranesToSync);
                $factura->update(['total' => $totalFactura]); // Actualizar el total de la factura
            }

            // Recargar para la respuesta
            $factura->load('cliente', 'albaranes.productos');

            return response()->json($factura);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Factura no encontrada.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar la factura: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Factura $factura)
    {
        try {
            // Eliminar la factura (esto también eliminará las entradas en factura_albarans por cascade onDelete)
            $factura->delete();

            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar la factura: ' . $e->getMessage()], 500);
        }
    }

    public function generarFacturasMensuales()
    {
        $clientes = Cliente::with(['albaranes' => function ($query) {
            // Obtener albaranes del mes y año actuales que NO estén ya facturados
            // Esto requiere una manera de marcar los albaranes como facturados.
            // Por simplicidad para este ejemplo, asumiremos que si un albarán
            // aparece en una factura para este mes, ya está facturado.
            // Una opción más robusta sería añadir un campo `facturado` (boolean) en Albaran
            // o un campo `factura_id` (nullable) en Albaran para indicar su vinculación.
            // Para este ejemplo, solo buscaremos albaranes del mes actual que no estén en ninguna factura de este mes.
            // (Esta es una lógica compleja si no hay un campo 'facturado' directo en Albaran)
            // Simplificación: Traemos todos los albaranes del mes y luego filtramos
            $query->whereMonth('fecha', now()->month)
                  ->whereYear('fecha', now()->year);
        }])->get();

        $facturasGeneradas = [];

        foreach ($clientes as $cliente) {
            // Filtrar albaranes que ya han sido incluidos en una factura para este mes para este cliente
            // Esta lógica puede ser compleja si no tienes un campo 'facturado' en albaranes.
            // Para una solución simple, vamos a generar una nueva factura si el cliente tiene albaranes
            // no facturados para este mes.
            // Alternativa robusta: Añadir un campo `facturado_at` (timestamp) en `albarans`
            // o `factura_id` (nullable) y filtrarlos.

            $albaranesNoFacturados = $cliente->albaranes->filter(function ($albaran) {
                // Comprobar si este albarán ya está asociado a una factura del mes actual.
                // Esto es una suposición, en un sistema real, un albarán debería tener un estado o link directo a la factura.
                return !$albaran->facturas->contains(function ($factura) {
                    return $factura->fecha->month == now()->month && $factura->fecha->year == now()->year;
                });
            });

            if ($albaranesNoFacturados->isEmpty()) {
                continue; // Si no hay albaranes del mes actual que no estén ya facturados, saltar al siguiente cliente
            }

            // Calcular el total de la nueva factura a partir de los albaranes no facturados
            $totalFactura = 0;
            $albaranesToAttach = [];
            foreach ($albaranesNoFacturados as $albaran) {
                // Asegúrate de cargar los productos si aún no están cargados
                $albaran->loadMissing('productos');
                $importeAlbaran = $albaran->productos->sum(function ($prod) {
                    return $prod->pivot->importe_total;
                });
                $totalFactura += $importeAlbaran;
                $albaranesToAttach[$albaran->id] = ['importe' => $importeAlbaran];
            }

            // Si el total es 0, no creamos la factura
            if ($totalFactura == 0) {
                continue;
            }

            // Crear la factura
            $factura = Factura::create([
                'fecha' => now(), // Fecha actual para la factura mensual
                'cliente_id' => $cliente->id,
                'total' => $totalFactura
            ]);

            // Adjuntar los albaranes a la factura
            $factura->albaranes()->attach($albaranesToAttach);
            $facturasGeneradas[] = $factura->id;
        }

        if (empty($facturasGeneradas)) {
            return response()->json(['status' => 'No se generaron nuevas facturas para este mes.'], 200);
        }

        return response()->json(['status' => 'Facturas mensuales generadas con éxito', 'facturas_ids' => $facturasGeneradas], 200);
    
    }

    public function indexWeb()
    {
        return view('facturas.index');
    }
    
    public function createWeb() // ¡Nuevo método!
    {
        return view('facturas.create');
    }
}
