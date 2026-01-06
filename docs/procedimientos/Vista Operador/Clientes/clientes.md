# Gestión de Clientes (Operador)

> **Estado:** ✅ OK  
> **Última actualización:** 2026-01-06

## Descripción

Vista para que operadores gestionen sus clientes asignados. Pueden crear, ver, editar y activar/desactivar clientes para que los programadores creen workflows.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/clients` |
| **Ruta nombrada** | `clients.index` |
| **Controlador** | `App\Http\Controllers\ClientController` |
| **Vista** | `resources/views/clients/index.blade.php` |
| **Layout** | `layouts/app` |
| **Middleware** | `auth`, `role:Super Admin\|Manager\|Programador\|Operador` |

---

## Funcionalidades CRUD

| Acción | Ruta | Método | Descripción |
|--------|------|--------|-------------|
| Listar | `/clients` | GET | Tabla de clientes |
| Crear | `/clients/create` | GET | Formulario nuevo cliente |
| Guardar | `/clients` | POST | Crear cliente |
| Ver | `/clients/{id}` | GET | Detalle con credenciales |
| Editar | `/clients/{id}/edit` | GET | Formulario edición |
| Actualizar | `/clients/{id}` | PUT | Guardar cambios |
| Eliminar | `/clients/{id}` | DELETE | Eliminar cliente |
| Desactivar | `/clients/{id}/deactivate` | POST | Marcar inactivo |
| Activar | `/clients/{id}/activate` | POST | Reactivar cliente |

---

## Modelo Relacionado

**Tabla:** `clients`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | PK |
| `company` | string | Nombre de la empresa |
| `contact_name` | string | Nombre del contacto |
| `email` | string | Email de contacto |
| `phone` | string | Teléfono |
| `address` | text | Dirección |
| `user_id` | bigint | FK - Operador asignado |
| `is_active` | boolean | Estado activo/inactivo |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Última modificación |

---

## Relaciones

- `Client belongsTo User` - Operador asignado
- `Client hasMany ClientCredential` - Credenciales de APIs
- `Client hasMany WorkflowFileBatch` - Lotes de workflows

---

## Vista de Detalle (`show`)

La vista de detalle (`/clients/{id}`) incluye:
- Información básica del cliente
- **Credenciales de APIs** - Gestión de credenciales para cada servicio
- **Historial de Workflows** - Ejecuciones realizadas para este cliente

---

## Permisos Requeridos

- Rol: `Operador` (u otros roles con acceso)
- Los operadores solo ven sus clientes asignados
- Administradores ven todos los clientes
