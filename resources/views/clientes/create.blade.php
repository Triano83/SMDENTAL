@extends('layouts.app')

@section('title', 'Crear Clínica')

@section('content')
    <h2>Crear Nueva Clínica</h2>

    <form id="createClientForm">
        @csrf {{-- Token CSRF para protección, aunque en API no es estrictamente necesario si solo consumes JSON --}}
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono">
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion">
        </div>
        <div class="mb-3">
            <label for="NIF" class="form-label">NIF</label>
            <input type="text" class="form-control" id="NIF" name="NIF">
        </div>
        <div class="mb-3">
            <label for="CP" class="form-label">Código Postal</label>
            <input type="text" class="form-control" id="CP" name="CP">
        </div>
        <div class="mb-3">
            <label for="poblacion" class="form-label">Población</label>
            <input type="text" class="form-control" id="poblacion" name="poblacion">
        </div>
        <div class="mb-3">
            <label for="provincia" class="form-label">Provincia</label>
            <input type="text" class="form-control" id="provincia" name="provincia">
        </div>
        <button type="submit" class="btn btn-primary">Guardar Clínica</button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection

@section('scripts')
<script>
    document.getElementById('createClientForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Evitar el envío tradicional del formulario

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/api/clientes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Cliente creado con éxito!');
                window.location.href = '{{ route('clientes.index') }}'; // Redirigir al listado
            } else {
                const errorData = await response.json();
                let errorMessage = 'Error al crear cliente:';
                if (errorData.errors) {
                    for (const key in errorData.errors) {
                        errorMessage += `\n- ${errorData.errors[key].join(', ')}`;
                    }
                } else if (errorData.message) {
                    errorMessage = 'Error: ' + errorData.message;
                }
                alert(errorMessage);
            }
        } catch (error) {
            console.error('Error de red o servidor:', error);
            alert('Hubo un problema al conectar con el servidor.');
        }
    });
</script>
@endsection