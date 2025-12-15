# Client Portal

Este proyecto es un **Portal de Clientes** desarrollado en Laravel, diseñado para gestionar clientes y usuarios con roles diferenciados (Administrador y Usuario).

## 🚀 Características Principales

*   **Autenticación y Seguridad**: Sistema de login y registro basado en Laravel Breeze y Sanctum.
*   **Gestión de Roles**:
    *   **Administrador**: Gestión completa de usuarios, configuración del sistema (correo) y estadísticas globales.
    *   **Usuario**: Gestión de su cartera de clientes (CRUD).
*   **Gestión de Clientes**:
    *   Alta, baja y modificación de clientes.
    *   Listado con filtros (Activos/Inactivos).
    *   Detalle de cliente con información fiscal y de contacto.
    *   Integración con WhatsApp para contacto directo.
*   **Diseño Moderno & Branding**:
    *   **Sidebar Layout**: Navegación vertical tipo Dashboard profesional.
    *   **Identidad Visual**: Paleta de colores corporativa (Azul Profundo `#0C263B` + Acento Coral `#FE9192`).
    *   **Estética Premium**: Uso de Glassmorphism, fondos texturizados y animaciones sutiles.
    *   **Interfaz Responsiva**: Adaptada a dispositivos móviles y escritorio con Tailwind CSS.

## 🛠️ Stack Tecnológico

*   **Backend**: Laravel 12.x, PHP 8.2+
*   **Frontend**: Blade, Tailwind CSS 3.x, Alpine.js, Vite
*   **Base de Datos**: SQLite (Por defecto) / MySQL / PostgreSQL
*   **Testing**: Pest PHP

## ⚙️ Instalación y Configuración

Sigue estos pasos para desplegar el proyecto en tu entorno local:

1.  **Clona el repositorio**:
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    cd nombre-del-proyecto
    ```

2.  **Instala las dependencias de PHP**:
    ```bash
    composer install
    ```

3.  **Instala las dependencias de Node.js**:
    ```bash
    npm install
    ```

4.  **Configura el entorno**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Prepara la base de datos (SQLite)**:
    ```bash
    # En Windows (Powershell)
    New-Item -Path database/database.sqlite -ItemType File
    
    # En Linux/Mac
    touch database/database.sqlite
    ```

6.  **Ejecuta las migraciones y seeders**:
    ```bash
    php artisan migrate:fresh --seed
    ```

7.  **Compila los assets**:
    ```bash
    npm run build
    ```

8.  **Ejecuta el servidor**:
    ```bash
    npm run dev
    # O en otra terminal
    php artisan serve
    ```

## 🔑 Credenciales de Acceso (Demo)

El seeder crea por defecto dos usuarios para pruebas:

| Rol | Email | Contraseña |
| :--- | :--- | :--- |
| **Administrador** | `admin@example.com` | `password` |
| **Usuario** | `user@example.com` | `password` |

## 🧪 Tests

El proyecto cuenta con una suite de tests automatizados para asegurar la integridad del sistema.

```bash
php artisan test
```