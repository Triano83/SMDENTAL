<?php

namespace App\Http\Controllers;

use App\Models\Albaran;
use App\Models\Cliente;
use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AlbaranController extends Controller
{
    public function index()
    {
        // Obtener todos los albaranes con sus clientes y productos asociados
        $albaranes = Albaran::with('cliente', 'productos')->get();
        return response()->json($albaranes);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada para el albarán
            $validatedAlbaranData = $request->validate([
                'fecha' => 'required|date',
                'cliente_id' => 'required|exists:clientes,id',
                'paciente' => 'nullable|string|max:100',
                'productos' => 'required|array|min:1', // Debe haber al menos un producto
                'productos.*.producto_id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                // 'productos.*.precio_unitario' y 'productos.*.importe_total'
                // Los calcularemos automáticamente en el backend para evitar manipulaciones.
            ]);

            // Buscar el cliente para asegurar que existe (aunque ya se valida con exists:clientes,id)
            $cliente = Cliente::find($validatedAlbaranData['cliente_id']);
            if (!$cliente) {
                return response()->json(['message' => 'Cliente no encontrado.'], 404);
            }

            // Crear el albarán
            $albaran = Albaran::create([
                'fecha' => $validatedAlbaranData['fecha'],
                'cliente_id' => $validatedAlbaranData['cliente_id'],
                'paciente' => $validatedAlbaranData['paciente'] ?? null,
            ]);

            // Preparar los datos para adjuntar productos al albarán
            $productosToAttach = [];
            foreach ($validatedAlbaranData['productos'] as $item) {
                $producto = Producto::find($item['producto_id']);
                if (!$producto) {
                    // Esto no debería ocurrir si 'exists:productos,id' funciona correctamente
                    // Pero es una buena práctica añadir un control de seguridad
                    $albaran->delete(); // Revertir la creación del albarán si un producto no existe
                    return response()->json(['message' => 'Producto con ID ' . $item['producto_id'] . ' no encontrado.'], 404);
                }

                $cantidad = $item['cantidad'];
                $precioUnitario = $producto->precio; // Usar el precio del producto de la base de datos
                $importeTotal = $cantidad * $precioUnitario;

                $productosToAttach[$producto->id] = [
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'importe_total' => $importeTotal,
                ];
            }

            // Adjuntar los productos al albarán con los datos pivote
            $albaran->productos()->attach($productosToAttach);

            // Cargar las relaciones para la respuesta
            $albaran->load('cliente', 'productos');

            return response()->json($albaran, 201); // 201 Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el albarán: ' . $e->getMessage()], 500);
        }
    }

    public function show(Albaran $albaran)
    {
        // Cargar las relaciones para mostrar el detalle completo
        $albaran->load('cliente', 'productos');
        return response()->json($albaran);
    }

    public function edit(Albaran $albaran)
    {
        //
    }

    public function update(Request $request, Albaran $albaran)
    {
        try {
            // Validar los datos de entrada para el albarán
            $validatedAlbaranData = $request->validate([
                'fecha' => 'sometimes|required|date',
                'cliente_id' => 'sometimes|required|exists:clientes,id',
                'paciente' => 'nullable|string|max:100',
                'productos' => 'sometimes|array|min:1', // Opcional, pero si se envía, debe tener al menos uno
                'productos.*.producto_id' => 'required_with:productos|exists:productos,id',
                'productos.*.cantidad' => 'required_with:productos|integer|min:1',
            ]);

            // Actualizar los campos del albarán
            $albaran->update([
                'fecha' => $validatedAlbaranData['fecha'] ?? $albaran->fecha,
                'cliente_id' => $validatedAlbaranData['cliente_id'] ?? $albaran->cliente_id,
                'paciente' => $validatedAlbaranData['paciente'] ?? $albaran->paciente,
            ]);

            // Si se envían productos para actualizar, los sincronizamos
            if (isset($validatedAlbaranData['productos'])) {
                $productosToSync = [];
                foreach ($validatedAlbaranData['productos'] as $item) {
                    $producto = Producto::find($item['producto_id']);
                    if (!$producto) {
                        return response()->json(['message' => 'Producto con ID ' . $item['producto_id'] . ' no encontrado.'], 404);
                    }

                    $cantidad = $item['cantidad'];
                    $precioUnitario = $producto->precio; // Usar el precio del producto de la base de datos
                    $importeTotal = $cantidad * $precioUnitario;

                    $productosToSync[$producto->id] = [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'importe_total' => $importeTotal,
                    ];
                }
                // Sync desvincula los productos que ya no están y adjunta/actualiza los nuevos
                $albaran->productos()->sync($productosToSync);
            }

            // Cargar las relaciones para la respuesta
            $albaran->load('cliente', 'productos');

            return response()->json($albaran);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Albarán no encontrado.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el albarán: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Albaran $albaran)
    {
        try {
            // Eliminar el albarán (esto también eliminará las entradas en albaran_productos por cascade onDelete)
            $albaran->delete();

            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el albarán: ' . $e->getMessage()], 500);
        }
    }

    public function indexWeb()
    {
        return view('albaranes.index');
    }

    public function createWeb() 
    {
        return view('albaranes.create');
    }

}
