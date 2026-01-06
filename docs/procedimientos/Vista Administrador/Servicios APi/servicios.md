# Servicios API

> **Estado:** ✅ OK  
> **Última actualización:** 2026-01-06

## Descripción

Vista de catálogo de servicios API disponibles en el sistema. Es **solo lectura** para administradores - pueden ver qué servicios existen pero no modificarlos.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/admin/api-services` |
| **Ruta nombrada** | `admin.api-services.index` |
| **Controlador** | `App\Http\Controllers\Admin\ApiServiceController` |
| **Vista** | `resources/views/admin/api-services/index.blade.php` |
| **Layout** | `layouts/admin` |
| **Middleware** | `auth`, `role:Super Admin\|Manager` |

---

## Funcionalidades

| Acción | Ruta | Método |
|--------|------|--------|
| Listar | `/admin/api-services` | GET |
| Ver detalle | `/admin/api-services/{id}` | GET |
| Crear | `/admin/api-services/create` | GET |
| Guardar | `/admin/api-services` | POST |
| Editar | `/admin/api-services/{id}/edit` | GET |
| Actualizar | `/admin/api-services/{id}` | PUT |
| Eliminar | `/admin/api-services/{id}` | DELETE |

> **Nota:** Aunque el controlador tiene CRUD completo, la idea es que el administrador solo use las funciones de lectura. La creación/edición es para configuración inicial del sistema.

---

## Modelo Relacionado

**Tabla:** `api_services`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | PK |
| `name` | string | Nombre del servicio |
| `description` | text | Descripción |
| `base_url` | string | URL base de la API |
| `required_fields` | json | Campos requeridos para credenciales |
| `is_active` | boolean | Estado activo/inactivo |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Última modificación |

---

## Relaciones

- `ApiService hasMany Endpoint` - Endpoints disponibles del servicio
- `ApiService hasMany ClientCredential` - Credenciales de clientes que usan este servicio

---

## Permisos Requeridos

- Rol: `Super Admin` o `Manager`
- Acceso: Menú "Servicios API" en navegación de administrador