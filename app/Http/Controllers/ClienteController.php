<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ClienteController extends Controller
{

    public function index()
    {
        // Obtener todos los clientes
        $clientes = Cliente::all();
        return response()->json($clientes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    { 
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:clientes',
                'telefono' => 'nullable|string|max:15',
                'direccion' => 'nullable|string|max:255',
                'NIF' => 'nullable|string|max:20',
                'CP' => 'nullable|string|max:20',
                'poblacion' => 'nullable|string|max:100',
                'provincia' => 'nullable|string|max:100',
            ]);

            // Crear el cliente
            $cliente = Cliente::create($validatedData);

            return response()->json($cliente, 201); // 201 Created
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422); // 422 Unprocessable Entity
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el cliente: ' . $e->getMessage()], 500);
        }
    }


    public function show(Cliente $cliente)
    {
        return response()->json($cliente);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:100',
                'email' => 'sometimes|required|string|email|max:100|unique:clientes,email,' . $cliente->id,
                'telefono' => 'nullable|string|max:15',
                'direccion' => 'nullable|string|max:255',
                'NIF' => 'nullable|string|max:20',
                'CP' => 'nullable|string|max:20',
                'poblacion' => 'nullable|string|max:100',
                'provincia' => 'nullable|string|max:100',
            ]);

            // Actualizar el cliente
            $cliente->update($validatedData);

            return response()->json($cliente);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el cliente: ' . $e->getMessage()], 500);
        }
    }


    public function destroy(Cliente $cliente)
    {
        try {
            // Eliminar el cliente
            $cliente->delete();

            return response()->json(null, 204); // 204 No Content
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el cliente: ' . $e->getMessage()], 500);
        }
    }
}
