@extends('layouts.app')

@section('title', 'Crear Albarán')

@section('content')
    <h2>Crear Nuevo Albarán</h2>

    <form id="createAlbaranForm">
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
        <div class="mb-3">
            <label for="paciente" class="form-label">Paciente</label>
            <input type="text" class="form-control" id="paciente" name="paciente" maxlength="100">
        </div>

        <hr>
        <h4>Productos del Albarán</h4>
        <div id="productos-container">
            <div class="row g-3 product-item mb-3 align-items-end border p-3 rounded">
                <div class="col-md-5">
                    <label for="producto_id_0" class="form-label">Producto <span class="text-danger">*</span></label>
                    <select class="form-select product-select" id="producto_id_0" name="productos[0][producto_id]" required>
                        <option value="">Selecciona un producto</option>
                        </select>
                </div>
                <div class="col-md-4">
                    <label for="cantidad_0" class="form-label">Cantidad <span class="text-danger">*</span></label>
                    <input type="number" class="form-control quantity-input" id="cantidad_0" name="productos[0][cantidad]" min="1" value="1" required>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-danger remove-product-btn">Eliminar</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-4" id="add-product-btn">Añadir Otro Producto</button>

        <button type="submit" class="btn btn-primary">Guardar Albarán</button>
        <a href="{{ route('albaranes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createAlbaranForm = document.getElementById('createAlbaranForm');
        const clienteSelect = document.getElementById('cliente_id');
        const productosContainer = document.getElementById('productos-container');
        const addProductBtn = document.getElementById('add-product-btn');
        let productIndex = 0; // Para manejar los índices de los campos de productos

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

        // Función para cargar productos en los selects de producto
        async function loadProductos(selectElement) {
            try {
                const response = await fetch('/api/productos');
                if (!response.ok) throw new Error('Error al cargar productos');
                const productos = await response.json();
                // Limpiar opciones existentes (excepto la primera "Selecciona un producto")
                while (selectElement.options.length > 1) {
                    selectElement.remove(1);
                }
                productos.forEach(producto => {
                    const option = document.createElement('option');
                    option.value = producto.id;
                    option.textContent = `${producto.nombre} (${parseFloat(producto.precio).toFixed(2)} €)`;
                    option.dataset.price = producto.precio; // Guardar el precio en el dataset
                    selectElement.appendChild(option);
                });
            } catch (error) {
                console.error('Error:', error);
                alert('No se pudieron cargar los productos. Intenta de nuevo más tarde.');
            }
        }

        // Función para añadir un nuevo bloque de producto
        function addProductItem() {
            productIndex++;
            const newItemHtml = `
                <div class="row g-3 product-item mb-3 align-items-end border p-3 rounded">
                    <div class="col-md-5">
                        <label for="producto_id_${productIndex}" class="form-label">Producto <span class="text-danger">*</span></label>
                        <select class="form-select product-select" id="producto_id_${productIndex}" name="productos[${productIndex}][producto_id]" required>
                            <option value="">Selecciona un producto</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="cantidad_${productIndex}" class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" class="form-control quantity-input" id="cantidad_${productIndex}" name="productos[${productIndex}][cantidad]" min="1" value="1" required>
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-danger remove-product-btn">Eliminar</button>
                    </div>
                </div>
            `;
            productosContainer.insertAdjacentHTML('beforeend', newItemHtml);

            // Cargar productos en el nuevo select
            const newProductSelect = document.getElementById(`producto_id_${productIndex}`);
            loadProductos(newProductSelect);

            // Añadir event listener al nuevo botón de eliminar
            newProductSelect.closest('.product-item').querySelector('.remove-product-btn').addEventListener('click', function() {
                this.closest('.product-item').remove();
            });
        }

        // Cargar clientes y productos iniciales
        loadClientes();
        loadProductos(document.getElementById('producto_id_0')); // Cargar productos para el primer select

        // Event listener para añadir productos
        addProductBtn.addEventListener('click', addProductItem);

        // Event listener para eliminar productos (delegación de eventos para botones añadidos dinámicamente)
        productosContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-product-btn')) {
                event.target.closest('.product-item').remove();
            }
        });

        // Manejar el envío del formulario
        createAlbaranForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const data = {
                fecha: formData.get('fecha'),
                cliente_id: formData.get('cliente_id'),
                paciente: formData.get('paciente'),
                productos: []
            };

            // Recopilar los productos y sus cantidades
            document.querySelectorAll('.product-item').forEach(item => {
                const productId = item.querySelector('.product-select').value;
                const quantity = item.querySelector('.quantity-input').value;
                if (productId && quantity) { // Asegurarse de que el producto y la cantidad estén seleccionados
                    data.productos.push({
                        producto_id: parseInt(productId),
                        cantidad: parseInt(quantity)
                    });
                }
            });

            // Validar que haya al menos un producto
            if (data.productos.length === 0) {
                alert('Debe añadir al menos un producto al albarán.');
                return;
            }

            try {
                const response = await fetch('/api/albaranes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert('Albarán creado con éxito!');
                    window.location.href = '{{ route('albaranes.index') }}'; // Redirigir al listado
                } else {
                    const errorData = await response.json();
                    let errorMessage = 'Error al crear albarán:';
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