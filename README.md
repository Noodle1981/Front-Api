# Admin SaaS - Sistema de Gestión de Clientes y APIs

¡Bienvenido al sistema de administración SaaS! Este proyecto ha sido modernizado y estructurado para ofrecer una gestión robusta de clientes, roles jerárquicos y automatización de integraciones.

## 🚀 Características Principales

### 1. Gestión de Clientes (CRUD Avanzado)
*   **Jerarquías**: Soporte para Sedes Centrales y Anexos/Sucursales.
*   **Datos de Negocio**: Clasificación por Rubro y Cantidad de Empleados.
*   **Estados**: Activación/Desactivación lógica (Soft Delete para Managers).

### 2. Seguridad y Roles (RBAC) 🛡️
Implementado con `spatie/laravel-permission`.
*   **Super Admin**: Acceso total + Panel de Administración (`/admin`).
*   **Manager**: Gestión completa de clientes y eliminaciones.
*   **Analista**: Operativo (Crear/Editar) pero sin permisos destructivos ni de admin.

### 3. Catálogo de APIs y Credenciales 🔑
*   **Admin**: Define qué servicios están disponibles (ej: Mercado Pago, AFIP) y qué campos requieren (API Key, Secret, etc.).
*   **Cliente**: Se le asignan credenciales encriptadas para cada servicio.
*   **Automatización**: Configuración de frecuencia de ejecución (Diaria/Semanal) y alertas por email personalizadas por conexión.

### 4. Interfaz Moderna (Aurora Theme) 🎨
*   Diseño "Glassmorphism" con Tailwind CSS.
*   Modo Oscuro/Futurista por defecto.
*   Componentes reactivos con Alpine.js.

---

## 🛠️ Instalación y Configuración

1.  **Requisitos**: PHP 8.2+, Composer, Node.js, MySQL.

2.  **Clonar y Dependencias**:
    ```bash
    git clone <repo_url>
    cd front-main
    composer install
    npm install && npm run build
    ```

3.  **Configuración de Entorno**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    # Configurar base de datos en .env
    ```

4.  **Base de Datos y Seeds**:
    IMPORTANTE: Este comando crea los roles y el usuario Super Admin inicial.
    ```bash
    php artisan migrate --seed
    ```
    *Usuario por defecto*: `admin@example.com` / `password`

5.  **Instalar API (Opcional si no se ha hecho)**:
    ```bash
    php artisan install:api
    ```

## 🤖 Webhooks y Automatización

### Webhook Receiver
El sistema escucha notificaciones en:
`POST /api/webhooks/{service_slug}`
*(Ej: /api/webhooks/mercado-pago)*

### Cron Jobs (Automatización Saliente)
Para que las frecuencias configuradas (Diario 08:00, etc.) funcionen, configurar el cron del servidor:
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Testing

Para verificar la seguridad de roles:
```bash
php artisan test
```

---

## 📝 Créditos
Desarrollado con Laravel 11, Tailwind CSS y Alpine.js.