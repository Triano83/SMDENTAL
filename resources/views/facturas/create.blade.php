@extends('layouts.app')

@section('title', 'Crear Factura')

@section('content')
    <h2>Crear Nueva Factura</h2>

    <form id="createFacturaForm">
        @csrf
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="mb-3">
            <label for="cliente_id" class="form-label">Clínica (Cliente) <span class="text-danger">*</span></label>
            <select class="form-select" id="cliente_id" name="cliente_id" required>
                <option value="">Selecciona una clínica</option>
                </select>
        </div>

        <hr>
        <h4>Albaranes del Cliente (para incluir en la factura)</h4>
        <div id="albaranes-list-for-factura" class="mb-4">
            <p class="text-muted">Selecciona una clínica para ver sus albaranes disponibles.</p>
            </div>

        <button type="submit" class="btn btn-primary">Guardar Factura</button>
        <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createFacturaForm = document.getElementById('createFacturaForm');
        const clienteSelect = document.getElementById('cliente_id');
        const albaranesListContainer = document.getElementById('albaranes-list-for-factura');

        // Función para cargar clientes en el select
        async function loadClientes() {
            try {
                const response = await fetch('/api/clientes');
                if (!response.ok) throw new Error('Error al cargar clientes');
                const clientes = await response.json();
                clientes.forEach(cliente => {
                    const option = document.createElement('option');
                    option.value = cliente.id;
                    option.textContent = cliente.nombre;
                    clienteSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error:', error);
                alert('No se pudieron cargar los clientes. Intenta de nuevo más tarde.');
            }
        }

        // Función para cargar albaranes de un cliente específico
        async function loadAlbaranesForClient(clienteId) {
            albaranesListContainer.innerHTML = '<p>Cargando albaranes...</p>';
            if (!clienteId) {
                albaranesListContainer.innerHTML = '<p class="text-muted">Selecciona una clínica para ver sus albaranes disponibles.</p>';
                return;
            }

            try {
                // Fetch de todos los albaranes y luego filtra en el cliente.
                // O mejor, si tuvieras una ruta como /api/clientes/{id}/albaranes
                // Por ahora, haremos el filtro en JS, asumiendo que /api/albaranes trae todos.
                const response = await fetch(`/api/albaranes?cliente_id=${clienteId}`); // Idealmente, tu API debería soportar este filtro
                if (!response.ok) throw new Error('Error al cargar albaranes del cliente');
                const albaranes = await response.json();

                // Filtrar albaranes que pertenecen al cliente seleccionado
                const albaranesDelCliente = albaranes.filter(albaran => albaran.cliente_id == clienteId);

                if (albaranesDelCliente.length === 0) {
                    albaranesListContainer.innerHTML = '<p>Este cliente no tiene albaranes disponibles para facturar.</p>';
                    return;
                }

                let albaranesHtml = '<div class="list-group">';
                albaranesDelCliente.forEach(albaran => {
                    // Calculamos el total del albarán para mostrarlo
                    const totalAlbaran = albaran.productos.reduce((sum, item) => sum + item.pivot.importe_total, 0);

                    albaranesHtml += `
                        <label class="list-group-item">
                            <input class="form-check-input me-1" type="checkbox" name="albaranes_ids[]" value="${albaran.id}">
                            Albarán #${albaran.id} - Fecha: ${albaran.fecha} - Paciente: ${albaran.paciente || 'N/A'} - Total: ${totalAlbaran.toFixed(2)} €
                        </label>
                    `;
                });
                albaranesHtml += '</div>';
                albaranesListContainer.innerHTML = albaranesHtml;

            } catch (error) {
                console.error('Error:', error);
                albaranesListContainer.innerHTML = '<p class="text-danger">Error al cargar los albaranes para este cliente.</p>';
            }
        }

        // Cargar clientes al iniciar la página
        loadClientes();

        // Event listener para cuando cambie la selección de cliente
        clienteSelect.addEventListener('change', function() {
            loadAlbaranesForClient(this.value);
        });

        // Manejar el envío del formulario
        createFacturaForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const selectedAlbaranes = [];
            document.querySelectorAll('input[name="albaranes_ids[]"]:checked').forEach(checkbox => {
                selectedAlbaranes.push(parseInt(checkbox.value));
            });

            if (selectedAlbaranes.length === 0) {
                alert('Debes seleccionar al menos un albarán para crear la factura.');
                return;
            }

            const data = {
                fecha: formData.get('fecha'),
                cliente_id: formData.get('cliente_id'),
                albaranes_ids: selectedAlbaranes
            };

            try {
                const response = await fetch('/api/facturas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert('Factura creada con éxito!');
                    window.location.href = '{{ route('facturas.index') }}'; // Redirigir al listado
                } else {
                    const errorData = await response.json();
                    let errorMessage = 'Error al crear factura:';
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
    });
</script>
@endsection