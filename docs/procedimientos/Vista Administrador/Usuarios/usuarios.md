# Gestión de Usuarios

> **Estado:** ✅ OK  
> **Última actualización:** 2026-01-06

## Descripción

Vista para que el administrador gestione usuarios del sistema: programadores y operadores. Permite crear, editar, activar/desactivar usuarios.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/admin/users` |
| **Ruta nombrada** | `admin.users.index` |
| **Controlador** | `App\Http\Controllers\Admin\UserController` |
| **Vista** | `resources/views/admin/users/index.blade.php` |
| **Layout** | `layouts/admin` |
| **Middleware** | `auth`, `role:Super Admin\|Manager` |

---

## Funcionalidades CRUD

| Acción | Ruta | Método | Descripción |
|--------|------|--------|-------------|
| Listar | `/admin/users` | GET | Tabla de usuarios |
| Crear | `/admin/users/create` | GET | Formulario nuevo usuario |
| Guardar | `/admin/users` | POST | Crear usuario |
| Ver | `/admin/users/{id}` | GET | Detalle de usuario |
| Editar | `/admin/users/{id}/edit` | GET | Formulario edición |
| Actualizar | `/admin/users/{id}` | PUT | Guardar cambios |
| Eliminar | `/admin/users/{id}` | DELETE | Eliminar usuario |

---

## Modelo Relacionado

**Tabla:** `users`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | PK |
| `name` | string | Nombre completo |
| `email` | string | Correo electrónico (único) |
| `password` | string | Contraseña hasheada |
| `is_active` | boolean | Estado activo/inactivo |
| `email_verified_at` | timestamp | Verificación de email |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Última modificación |

---

## Roles del Sistema

Usando **Spatie Permission**:

| Rol | Descripción |
|-----|-------------|
| `Super Admin` | Acceso total al sistema |
| `Manager` | Administrador con acceso al panel admin |
| `Programador` | Crea workflows y reglas de negocio |
| `Operador` | Gestiona clientes y ejecuta workflows |

---

## Relaciones

- `User hasMany Client` - Clientes asignados (para Operadores)
- `User hasMany WorkflowFileBatch` - Lotes de workflows creados
- `User belongsToMany Role` - Roles asignados (Spatie)

---

## Permisos Requeridos

- Rol: `Super Admin` o `Manager`
- Acceso: Menú "Usuarios" en navegación de administrador

---

## Notas

- Al crear un usuario, se debe asignar un rol
- Los usuarios desactivados no pueden iniciar sesión
- No se pueden eliminar usuarios con clientes o workflows asociados