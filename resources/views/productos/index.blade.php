@extends('layouts.app')

@section('title', 'Listar Productos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Productos</h2>
        <a href="{{ route('productos.create') }}" class="btn btn-success">Crear Nuevo Producto</a>
    </div>

    <div id="productos-list">
        <p>Cargando productos...</p>
    </div>

    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editProductId">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editProductName" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="editProductPrice" name="precio" required>
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
        const productosListDiv = document.getElementById('productos-list');
        const editProductModal = new bootstrap.Modal(document.getElementById('editProductModal'));
        const editProductForm = document.getElementById('editProductForm');

        // Función para cargar productos
        async function loadProductos() {
            try {
                const response = await fetch('/api/productos');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const productos = await response.json();
                renderProductos(productos);
            } catch (error) {
                console.error('Error al cargar productos:', error);
                productosListDiv.innerHTML = '<p class="text-danger">Error al cargar los productos.</p>';
            }
        }

        // Función para renderizar productos en la tabla
        function renderProductos(productos) {
            if (productos.length === 0) {
                productosListDiv.innerHTML = '<p>No hay productos registrados.</p>';
                return;
            }

            let tableHtml = `
                <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            productos.forEach(producto => {
                tableHtml += `
                    <tr>
                        <td>${producto.id}</td>
                        <td>${producto.nombre}</td>
                        <td>${parseFloat(producto.precio).toFixed(2)} €</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-btn" data-id="${producto.id}"
                                data-nombre="${producto.nombre}"
                                data-precio="${producto.precio}"
                            >Editar</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${producto.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            tableHtml += `
                    </tbody>
                </table>
                </div>
            `;
            productosListDiv.innerHTML = tableHtml;

            // Añadir event listeners a los botones de editar y eliminar
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    document.getElementById('editProductId').value = id;
                    document.getElementById('editProductName').value = this.dataset.nombre;
                    document.getElementById('editProductPrice').value = this.dataset.precio;
                    editProductModal.show();
                });
            });

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
                        const id = this.dataset.id;
                        try {
                            const response = await fetch(`/api/productos/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            if (response.status === 204) {
                                loadProductos(); // Recargar la lista
                            } else {
                                const errorData = await response.json();
                                alert('Error al eliminar el producto: ' + (errorData.message || 'Error desconocido'));
                            }
                        } catch (error) {
                            console.error('Error al eliminar producto:', error);
                            alert('Error de red o servidor al eliminar el producto.');
                        }
                    }
                });
            });
        }

        // Manejar el envío del formulario de edición
        editProductForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const productId = document.getElementById('editProductId').value;
            const formData = new FormData(editProductForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`/api/productos/${productId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    editProductModal.hide();
                    loadProductos(); // Recargar la lista de productos
                } else {
                    const errorData = await response.json();
                    alert('Error al actualizar: ' + JSON.stringify(errorData.errors || errorData.message));
                }
            } catch (error) {
                console.error('Error al actualizar producto:', error);
                alert('Error de red o servidor al actualizar el producto.');
            }
        });

        // Cargar productos al iniciar la página
        loadProductos();
    });
</script>
@endsection