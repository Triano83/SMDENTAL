<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/your-username/your-repo/actions">
    <img src="https://github.com/your-username/your-repo/workflows/PHPUnit/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
  <a href="https://laravel.com">
    <img src="https://img.shields.io/badge/PHP-%3E%3D8.2-8892BF.svg" alt="PHP Version">
  </a>
</p>

# 🦷 S.M. Dental: Sistema de Gestión y Facturación para Clínicas Dentales

---

## ✨ Visión General

**S.M. Dental** es una aplicación robusta y eficiente desarrollada con **Laravel** para la gestión integral de la facturación en clínicas dentales. Diseñada para simplificar el proceso de creación y administración de clientes, productos, albaranes y facturas, esta plataforma busca optimizar las operaciones diarias y asegurar un control financiero preciso.

Con una interfaz de usuario clara y una API RESTful bien definida, S.M. Dental es la solución ideal para automatizar tareas repetitivas y proporcionar una visión completa de la actividad económica de la clínica.

---

## 🚀 Características Principales

* **Gestión de Clientes (Clínicas)**:
    * ➕ **Creación** y **Listado** de clínicas con información detallada (nombre, email, teléfono, dirección, NIF, etc.).
    * ✏️ **Edición** y **Eliminación** de clientes existentes.
* **Gestión de Productos**:
    * ➕ **Creación** y **Listado** de productos con su nombre y precio.
    * ✏️ **Edición** y **Eliminación** de productos.
* **Gestión de Albaranes**:
    * ➕ **Creación de Albaranes** asociados a un cliente, permitiendo añadir múltiples productos con sus cantidades.
    * 📄 **Listado** de albaranes con detalle de cliente y productos incluidos.
    * 🗑️ **Eliminación** de albaranes.
* **Gestión de Facturas**:
    * ➕ **Creación Manual de Facturas** a partir de uno o varios albaranes seleccionados de un cliente.
    * 🤖 **Generación Automática de Facturas Mensuales**: Crea facturas para todos los clientes que tienen albaranes pendientes del mes actual.
    * 📊 **Listado** de facturas con el total, cliente asociado y albaranes que la componen.
    * 🗑️ **Eliminación** de facturas.
* **API RESTful**: Un conjunto completo de endpoints para una integración sencilla con otras aplicaciones o servicios.
* **Interfaz de Usuario (Blade con Bootstrap 5)**: Una experiencia de usuario intuitiva y responsive para la gestión diaria.

---

## 📦 Estructura del Proyecto

El proyecto sigue la arquitectura MVC de Laravel, organizada de la siguiente manera:

* **`routes/web.php`**: Define las rutas para las vistas web (Blade) y las rutas de la API (`apiResource`).
* **`app/Models/`**: Contiene los modelos Eloquent (`Cliente`, `Producto`, `Albaran`, `Factura`) con sus relaciones (`hasMany`, `belongsTo`, `belongsToMany`) correctamente definidas.
* **`database/migrations/`**: Esquemas de la base de datos para todas las entidades, incluyendo tablas pivote (`albaran_productos`, `factura_albarans`) para relaciones muchos-a-muchos.
* **`app/Http/Controllers/`**: Controladores que manejan la lógica de negocio, tanto para las peticiones de la API (JSON) como para servir las vistas (HTML).
* **`resources/views/`**: Plantillas Blade para la interfaz de usuario, organizadas por módulos (`layouts`, `clientes`, `productos`, `albaranes`, `facturas`). Utiliza JavaScript (Fetch API) para interactuar con los endpoints de la API.

---

## 🛠️ Requisitos del Sistema

* **PHP**: ^8.2
* **Composer**
* **Node.js y npm** (o Yarn)
* **Base de Datos**: MySQL, PostgreSQL, SQLite (configurado en `.env`)

---

## ⚙️ Instalación y Configuración

Sigue estos pasos para poner en marcha el proyecto en tu entorno local:

1.  **Clonar el Repositorio**:
    ```bash
    git clone [https://github.com/your-username/sm-dental.git](https://github.com/your-username/sm-dental.git)
    cd sm-dental
    ```
    *(Asegúrate de cambiar `your-username/sm-dental` por la URL real de tu repositorio)*

2.  **Instalar Dependencias de Composer**:
    ```bash
    composer install
    ```

3.  **Configurar el Archivo `.env`**:
    * Copia el archivo de ejemplo:
        ```bash
        cp .env.example .env
        ```
    * Genera una clave de aplicación:
        ```bash
        php artisan key:generate
        ```
    * Abre el archivo `.env` y configura la conexión a tu base de datos:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=sm_dental_db # Puedes usar el nombre que prefieras
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Ejecutar Migraciones de Base de Datos**:
    ```bash
    php artisan migrate
    ```
    *(Esto creará todas las tablas necesarias en tu base de datos.)*

5.  **Instalar Dependencias de Node (para Bootstrap CSS/JS)**:
    ```bash
    npm install
    # o yarn install
    ```
    *(Aunque actualmente se usan CDN para Bootstrap, para futuras personalizaciones o compilación de assets, esto sería necesario.)*

6.  **Iniciar el Servidor de Desarrollo**:
    ```bash
    php artisan serve
    ```
    La aplicación estará disponible en `http://127.0.0.1:8000`.

---

## 📝 Uso de la Aplicación

Una vez que el servidor esté en marcha, navega a la URL local (ej. `http://127.0.0.1:8000`).

* Utiliza la **barra de navegación superior** para acceder a las secciones:
    * **Crear/Listar Clínicas**: Para gestionar tus clientes dentales.
    * **Crear/Listar Productos**: Para definir los servicios o artículos que ofreces.
    * **Crear/Listar Albaranes**: Para registrar las entregas o servicios realizados a los clientes.
    * **Crear/Listar Facturas**: Para generar y consultar las facturas.
    * **Generar Facturas Mensuales**: Un botón conveniente para automatizar la creación de facturas para los albaranes pendientes del mes actual.

---

## 🌐 Endpoints de la API (RESTful)

Para interactuar con la aplicación programáticamente, puedes usar los siguientes endpoints:

| Recurso    | Método | Ruta                     | Descripción                                         |
| :--------- | :----- | :----------------------- | :-------------------------------------------------- |
| `clientes` | `GET`  | `/api/clientes`          | Obtener todos los clientes                          |
|            | `POST` | `/api/clientes`          | Crear un nuevo cliente                              |
|            | `GET`  | `/api/clientes/{id}`     | Obtener un cliente específico                       |
|            | `PUT`  | `/api/clientes/{id}`     | Actualizar un cliente específico                    |
|            | `DELETE` | `/api/clientes/{id}`     | Eliminar un cliente específico                      |
| `productos`| `GET`  | `/api/productos`         | Obtener todos los productos                         |
|            | `POST` | `/api/productos`         | Crear un nuevo producto                             |
|            | `GET`  | `/api/productos/{id}`    | Obtener un producto específico                      |
|            | `PUT`  | `/api/productos/{id}`    | Actualizar un producto específico                   |
|            | `DELETE` | `/api/productos/{id}`    | Eliminar un producto específico                     |
| `albaranes`| `GET`  | `/api/albaranes`         | Obtener todos los albaranes (con cliente y productos) |
|            | `POST` | `/api/albaranes`         | Crear un nuevo albarán (con productos anidados)     |
|            | `GET`  | `/api/albaranes/{id}`    | Obtener un albarán específico                       |
|            | `PUT`  | `/api/albaranes/{id}`    | Actualizar un albarán específico                    |
|            | `DELETE` | `/api/albaranes/{id}`    | Eliminar un albarán específico                      |
| `facturas` | `GET`  | `/api/facturas`          | Obtener todas las facturas (con cliente y albaranes) |
|            | `POST` | `/api/facturas`          | Crear una nueva factura (a partir de albaranes)     |
|            | `GET`  | `/api/facturas/{id}`     | Obtener una factura específica                      |
|            | `PUT`  | `/api/facturas/{id}`     | Actualizar una factura específica                   |
|            | `DELETE` | `/api/facturas/{id}`     | Eliminar una factura específica                     |
| `facturas` | `POST` | `/facturas/generar-mensual`| Generar facturas mensuales automáticas              |

---

## 📈 Diagrama de Relación de Entidades (ERD)

Para comprender mejor cómo se relacionan las tablas en la base de datos, aquí tienes un esquema simplificado.

```mermaid
erDiagram
    CLIENTES ||--o{ ALBARANES : ""
    CLIENTES ||--o{ FACTURAS : ""
    ALBARANES ||--o{ ALBARAN_PRODUCTOS : ""
    PRODUCTOS ||--o{ ALBARAN_PRODUCTOS : ""
    FACTURAS ||--o{ FACTURA_ALBARANES : ""
    ALBARANES ||--o{ FACTURA_ALBARANES : ""

    CLIENTES {
        int id PK
        varchar nombre
        varchar email
        varchar telefono
        varchar direccion
        varchar NIF
        varchar CP
        varchar poblacion
        varchar provincia
        timestamp created_at
        timestamp updated_at
    }

    PRODUCTOS {
        int id PK
        varchar nombre
        decimal precio
        timestamp created_at
        timestamp updated_at
    }

    ALBARANES {
        int id PK
        date fecha
        int cliente_id FK
        varchar paciente
        timestamp created_at
        timestamp updated_at
    }

    ALBARAN_PRODUCTOS {
        int id PK
        int albaran_id FK
        int producto_id FK
        int cantidad
        decimal precio_unitario
        decimal importe_total
        timestamp created_at
        timestamp updated_at
    }

    FACTURAS {
        int id PK
        date fecha
        int cliente_id FK
        decimal total
        timestamp created_at
        timestamp updated_at
    }

    FACTURA_ALBARANES {
        int id PK
        int factura_id FK
        int albaran_id FK
        decimal importe
        timestamp created_at
        timestamp updated_at
    }