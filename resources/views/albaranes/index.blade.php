@extends('layouts.app')

@section('title', 'Listar Albaranes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Albaranes</h2>
        {{-- Aquí iría el botón para crear albarán, lo implementaremos más adelante --}}
        {{-- <a href="{{ route('albaranes.create') }}" class="btn btn-warning">Crear Nuevo Albarán</a> --}}
    </div>

    <div id="albaranes-list">
        <p>Cargando albaranes...</p>
    </div>

    <div class="modal fade" id="viewAlbaranModal" tabindex="-1" aria-labelledby="viewAlbaranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAlbaranModalLabel">Detalles del Albarán</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="albaranDetailsContent">
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
        const albaranesListDiv = document.getElementById('albaranes-list');
        const viewAlbaranModal = new bootstrap.Modal(document.getElementById('viewAlbaranModal'));
        const albaranDetailsContent = document.getElementById('albaranDetailsContent');

        // Función para cargar albaranes
        async function loadAlbaranes() {
            try {
                const response = await fetch('/api/albaranes');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const albaranes = await response.json();
                renderAlbaranes(albaranes);
            } catch (error) {
                console.error('Error al cargar albaranes:', error);
                albaranesListDiv.innerHTML = '<p class="text-danger">Error al cargar los albaranes.</p>';
            }
        }

        // Función para renderizar albaranes en la tabla
        function renderAlbaranes(albaranes) {
            if (albaranes.length === 0) {
                albaranesListDiv.innerHTML = '<p>No hay albaranes registrados.</p>';
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
                            <th>Paciente</th>
                            <th>Total Productos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            albaranes.forEach(albaran => {
                const totalProductos = albaran.productos.reduce((sum, item) => sum + item.pivot.importe_total, 0);
                tableHtml += `
                    <tr>
                        <td>${albaran.id}</td>
                        <td>${albaran.fecha}</td>
                        <td>${albaran.cliente ? albaran.cliente.nombre : 'N/A'}</td>
                        <td>${albaran.paciente || 'N/A'}</td>
                        <td>${totalProductos.toFixed(2)} €</td>
                        <td>
                            <button class="btn btn-sm btn-info view-btn" data-id="${albaran.id}">Ver</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${albaran.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            tableHtml += `
                    </tbody>
                </table>
                </div>
            `;
            albaranesListDiv.innerHTML = tableHtml;

            // Añadir event listeners a los botones de ver y eliminar
            document.querySelectorAll('.view-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    const id = this.dataset.id;
                    await fetchAlbaranDetails(id);
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (confirm('¿Estás seguro de que quieres eliminar este albarán?')) {
                        const id = this.dataset.id;
                        try {
                            const response = await fetch(`/api/albaranes/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.status === 204) {
                                loadAlbaranes(); // Recargar la lista
                            } else {
                                const errorData = await response.json();
                                alert('Error al eliminar el albarán: ' + (errorData.message || 'Error desconocido'));
                            }
                        } catch (error) {
                            console.error('Error al eliminar albarán:', error);
                            alert('Error de red o servidor al eliminar el albarán.');
                        }
                    }
                });
            });
        }

        // Función para cargar y mostrar detalles de un albarán
        async function fetchAlbaranDetails(id) {
            try {
                const response = await fetch(`/api/albaranes/${id}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const albaran = await response.json();
                
                let productsHtml = '';
                let albaranTotal = 0;

                if (albaran.productos && albaran.productos.length > 0) {
                    productsHtml = `
                        <h6 class="mt-3">Productos en este Albarán:</h6>
                        <ul class="list-group mb-3">
                    `;
                    albaran.productos.forEach(prod => {
                        const subtotal = parseFloat(prod.pivot.importe_total).toFixed(2);
                        albaranTotal += parseFloat(prod.pivot.importe_total);
                        productsHtml += `
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                ${prod.nombre} (x${prod.pivot.cantidad})
                                <span class="badge bg-primary rounded-pill">${subtotal} €</span>
                            </li>
                        `;
                    });
                    productsHtml += `</ul>`;
                } else {
                    productsHtml = '<p>No hay productos asociados a este albarán.</p>';
                }

                albaranDetailsContent.innerHTML = `
                    <p><strong>ID Albarán:</strong> ${albaran.id}</p>
                    <p><strong>Fecha:</strong> ${albaran.fecha}</p>
                    <p><strong>Clínica:</strong> ${albaran.cliente ? albaran.cliente.nombre : 'N/A'}</p>
                    <p><strong>Paciente:</strong> ${albaran.paciente || 'N/A'}</p>
                    ${productsHtml}
                    <p><strong>Total Albarán:</strong> <strong>${albaranTotal.toFixed(2)} €</strong></p>
                `;
                viewAlbaranModal.show();
            } catch (error) {
                console.error('Error al cargar detalles del albarán:', error);
                albaranDetailsContent.innerHTML = '<p class="text-danger">Error al cargar los detalles del albarán.</p>';
            }
        }

        // Cargar albaranes al iniciar la página
        loadAlbaranes();
    });
</script>
@endsection