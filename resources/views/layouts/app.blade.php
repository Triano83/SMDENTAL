<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>S.M. Dental - @yield('title', 'Gestión')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1; /* Permite que el contenido principal ocupe el espacio restante */
        }
        .navbar-nav .btn {
            min-width: 120px; /* Asegura un ancho mínimo para los botones de la navbar */
            margin-bottom: 5px; /* Pequeño margen para pantallas pequeñas */
        }
        @media (min-width: 992px) { /* Para pantallas grandes, volver a alinear los botones */
            .navbar-nav .btn {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>

    <header class="bg-primary text-white text-center py-3">
        <h1>S.M. Dental</h1>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Inicio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto"> {{-- ms-auto empuja los elementos a la derecha --}}
                    <li class="nav-item mx-1">
                        <a class="btn btn-primary" href="{{ route('clientes.create') }}">Crear Clínica</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn btn-secondary" href="{{ route('clientes.index') }}">Listar Clínicas</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn btn-success" href="{{ route('productos.create') }}">Crear Producto</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn btn-info" href="{{ route('productos.index') }}">Listar Productos</a>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn btn-warning" href="{{ route('albaranes.index') }}">Listar Albaranes</a>
                        {{-- La creación de albaranes será más compleja y la haremos después --}}
                    </li>
                    <li class="nav-item mx-1">
                        <a class="btn btn-danger" href="{{ route('facturas.index') }}">Listar Facturas</a>
                        {{-- La creación de facturas será más compleja y la haremos después --}}
                    </li>
                    <li class="nav-item mx-1">
                        <form action="{{ url('facturas/generar-mensual') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-dark">Generar Facturas Mensuales</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        @yield('content')
    </main>

    <footer class="bg-light text-center py-3 mt-auto">
        <div class="container">
            <p>&copy; {{ date('Y') }} Jose Luis Triano Pavón. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts') {{-- Para scripts específicos de cada vista --}}
</body>
</html>