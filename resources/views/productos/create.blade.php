@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
    <h2>Crear Nuevo Producto</h2>

    <form id="createProductForm">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio <span class="text-danger">*</span></label>
            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Producto</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection

@section('scripts')
<script>
    document.getElementById('createProductForm').addEventListener('submit', async function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('/api/productos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('Producto creado con éxito!');
                window.location.href = '{{ route('productos.index') }}';
            } else {
                const errorData = await response.json();
                let errorMessage = 'Error al crear producto:';
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