@extends('layouts.app')

@section('title', 'Listar Clínicas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Clínicas</h2>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">Crear Nueva Clínica</a>
    </div>

    <div id="clientes-list">
        <p>Cargando clientes...</p>
    </div>

    <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClientModalLabel">Editar Clínica</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editClientForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editClientId">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTelefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="editTelefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="editDireccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="editDireccion" name="direccion">
                        </div>
                        <div class="mb-3">
                            <label for="editNIF" class="form-label">NIF</label>
                            <input type="text" class="form-control" id="editNIF" name="NIF">
                        </div>
                        <div class="mb-3">
                            <label for="editCP" class="form-label">Código Postal</label>
                            <input type="text" class="form-control" id="editCP" name="CP">
                        </div>
                        <div class="mb-3">
                            <label for="editPoblacion" class="form-label">Población</label>
                            <input type="text" class="form-control" id="editPoblacion" name="poblacion">
                        </div>
                        <div class="mb-3">
                            <label for="editProvincia" class="form-label">Provincia</label>
                            <input type="text" class="form-control" id="editProvincia" name="provincia">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const clientesListDiv = document.getElementById('clientes-list');
        const editClientModal = new bootstrap.Modal(document.getElementById('editClientModal'));
        const editClientForm = document.getElementById('editClientForm');

        // Función para cargar clientes
        async function loadClientes() {
            try {
                const response = await fetch('/api/clientes');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const clientes = await response.json();
                renderClientes(clientes);
            } catch (error) {
                console.error('Error al cargar clientes:', error);
                clientesListDiv.innerHTML = '<p class="text-danger">Error al cargar los clientes.</p>';
            }
        }

        // Función para renderizar clientes en la tabla
        function renderClientes(clientes) {
            if (clientes.length === 0) {
                clientesListDiv.innerHTML = '<p>No hay clientes registrados.</p>';
                return;
            }

            let tableHtml = `
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>NIF</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            clientes.forEach(cliente => {
                tableHtml += `
                    <tr>
                        <td>${cliente.id}</td>
                        <td>${cliente.nombre}</td>
                        <td>${cliente.email}</td>
                        <td>${cliente.telefono || 'N/A'}</td>
                        <td>${cliente.direccion || 'N/A'}</td>
                        <td>${cliente.NIF || 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" data-id="${cliente.id}"
                                data-nombre="${cliente.nombre}"
                                data-email="${cliente.email}"
                                data-telefono="${cliente.telefono || ''}"
                                data-direccion="${cliente.direccion || ''}"
                                data-nif="${cliente.NIF || ''}"
                                data-cp="${cliente.CP || ''}"
                                data-poblacion="${cliente.poblacion || ''}"
                                data-provincia="${cliente.provincia || ''}"
                            >Editar</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${cliente.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            tableHtml += `
                    </tbody>
                </table>
                </div>
            `;
            clientesListDiv.innerHTML = tableHtml;

            // Añadir event listeners a los botones de editar y eliminar
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    document.getElementById('editClientId').value = id;
                    document.getElementById('editNombre').value = this.dataset.nombre;
                    document.getElementById('editEmail').value = this.dataset.email;
                    document.getElementById('editTelefono').value = this.dataset.telefono;
                    document.getElementById('editDireccion').value = this.dataset.direccion;
                    document.getElementById('editNIF').value = this.dataset.nif;
                    document.getElementById('editCP').value = this.dataset.cp;
                    document.getElementById('editPoblacion').value = this.dataset.poblacion;
                    document.getElementById('editProvincia').value = this.dataset.provincia;
                    editClientModal.show();
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (confirm('¿Estás seguro de que quieres eliminar este cliente?')) {
                        const id = this.dataset.id;
                        try {
                            const response = await fetch(`/api/clientes/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.status === 204) { // 204 No Content para eliminación exitosa
                                loadClientes(); // Recargar la lista
                            } else {
                                const errorData = await response.json();
                                alert('Error al eliminar el cliente: ' + (errorData.message || 'Error desconocido'));
                            }
                        } catch (error) {
                            console.error('Error al eliminar cliente:', error);
                            alert('Error de red o servidor al eliminar el cliente.');
                        }
                    }
                });
            });
        }

        // Manejar el envío del formulario de edición
        editClientForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const clientId = document.getElementById('editClientId').value;
            const formData = new FormData(editClientForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`/api/clientes/${clientId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    editClientModal.hide();
                    loadClientes(); // Recargar la lista de clientes
                } else {
                    const errorData = await response.json();
                    alert('Error al actualizar: ' + JSON.stringify(errorData.errors || errorData.message));
                }
            } catch (error) {
                console.error('Error al actualizar cliente:', error);
                alert('Error de red o servidor al actualizar el cliente.');
            }
        });

        // Cargar clientes al iniciar la página
        loadClientes();
    });
</script>
@endsection
