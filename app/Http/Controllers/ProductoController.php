<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ProductoController extends Controller
{

    public function index()
    {
        // Obtener todos los productos
        $productos = Producto::all();
        return response()->json($productos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:100',
                'precio' => 'required|numeric|min:0',
            ]);

            // Crear el producto
            $producto = Producto::create($validatedData);

            return response()->json($producto, 201); // 201 Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el producto: ' . $e->getMessage()], 500);
        }
    }


    public function show(Producto $producto)
    {
      // Devolver el producto específico
        return response()->json($producto);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }


    public function update(Request $request, Producto $producto)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:100',
                'precio' => 'sometimes|required|numeric|min:0',
            ]);

            // Actualizar el producto
            $producto->update($validatedData);

            return response()->json($producto);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el producto: ' . $e->getMessage()], 500);
        }
    }


    public function destroy(Producto $producto)
    {
        try {
            // Eliminar el producto
            $producto->delete();

            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el producto: ' . $e->getMessage()], 500);
        }
    }
}
