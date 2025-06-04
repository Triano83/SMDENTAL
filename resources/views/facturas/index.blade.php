@extends('layouts.app')

@section('title', 'Listar Facturas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Facturas</h2>
        {{-- Aquí iría el botón para crear factura, lo implementaremos más adelante --}}
        {{-- <a href="{{ route('facturas.create') }}" class="btn btn-dark">Crear Nueva Factura</a> --}}
    </div>

    <div id="facturas-list">
        <p>Cargando facturas...</p>
    </div>

    <div class="modal fade" id="viewFacturaModal" tabindex="-1" aria-labelledby="viewFacturaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewFacturaModalLabel">Detalles de la Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="facturaDetailsContent">
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const facturasListDiv = document.getElementById('facturas-list');
        const viewFacturaModal = new bootstrap.Modal(document.getElementById('viewFacturaModal'));
        const facturaDetailsContent = document.getElementById('facturaDetailsContent');

        // Función para cargar facturas
        async function loadFacturas() {
            try {
                const response = await fetch('/api/facturas');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const facturas = await response.json();
                renderFacturas(facturas);
            } catch (error) {
                console.error('Error al cargar facturas:', error);
                facturasListDiv.innerHTML = '<p class="text-danger">Error al cargar las facturas.</p>';
            }
        }

        // Función para renderizar facturas en la tabla
        function renderFacturas(facturas) {
            if (facturas.length === 0) {
                facturasListDiv.innerHTML = '<p>No hay facturas registradas.</p>';
                return;
            }

            let tableHtml = `
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Clínica</th>
                            <th>Total Factura</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            facturas.forEach(factura => {
                tableHtml += `
                    <tr>
                        <td>${factura.id}</td>
                        <td>${factura.fecha}</td>
                        <td>${factura.cliente ? factura.cliente.nombre : 'N/A'}</td>
                        <td>${parseFloat(factura.total).toFixed(2)} €</td>
                        <td>
                            <button class="btn btn-sm btn-info view-btn" data-id="${factura.id}">Ver</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${factura.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            tableHtml += `
                    </tbody>
                </table>
                </div>
            `;
            facturasListDiv.innerHTML = tableHtml;

            // Añadir event listeners a los botones de ver y eliminar
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const id = this.dataset.id;
                    await fetchFacturaDetails(id);
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (confirm('¿Estás seguro de que quieres eliminar esta factura?')) {
                        const id = this.dataset.id;
                        try {
                            const response = await fetch(`/api/facturas/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.status === 204) {
                                loadFacturas(); // Recargar la lista
                            } else {
                                const errorData = await response.json();
                                alert('Error al eliminar la factura: ' + (errorData.message || 'Error desconocido'));
                            }
                        } catch (error) {
                            console.error('Error al eliminar factura:', error);
                            alert('Error de red o servidor al eliminar la factura.');
                        }
                    }
                });
            });
        }

        // Función para cargar y mostrar detalles de una factura
        async function fetchFacturaDetails(id) {
            try {
                const response = await fetch(`/api/facturas/${id}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const factura = await response.json();
                
                let albaranesHtml = '';
                if (factura.albaranes && factura.albaranes.length > 0) {
                    albaranesHtml = `
                        <h6 class="mt-3">Albaranes incluidos en esta Factura:</h6>
                        <ul class="list-group mb-3">
                    `;
                    factura.albaranes.forEach(albaran => {
                        const albaranImporte = parseFloat(albaran.pivot.importe).toFixed(2);
                        albaranesHtml += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Albarán #${albaran.id} (${albaran.fecha}) - Paciente: ${albaran.paciente || 'N/A'}
                                <span class="badge bg-success rounded-pill">${albaranImporte} €</span>
                            </li>
                        `;
                    });
                    albaranesHtml += `</ul>`;
                } else {
                    albaranesHtml = '<p>No hay albaranes asociados a esta factura.</p>';
                }

                facturaDetailsContent.innerHTML = `
                    <p><strong>ID Factura:</strong> ${factura.id}</p>
                    <p><strong>Fecha:</strong> ${factura.fecha}</p>
                    <p><strong>Clínica:</strong> ${factura.cliente ? factura.cliente.nombre : 'N/A'}</p>
                    ${albaranesHtml}
                    <p><strong>Total Factura:</strong> <strong>${parseFloat(factura.total).toFixed(2)} €</strong></p>
                `;
                viewFacturaModal.show();
            } catch (error) {
                console.error('Error al cargar detalles de la factura:', error);
                facturaDetailsContent.innerHTML = '<p class="text-danger">Error al cargar los detalles de la factura.</p>';
            }
        }

        // Cargar facturas al iniciar la página
        loadFacturas();
    });
</script>
@endsection