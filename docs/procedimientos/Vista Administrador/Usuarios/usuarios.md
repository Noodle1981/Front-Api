# Gestión de Usuarios

> **Ruta:** `/admin/users`  
> **Acceso:** Super Admin, Manager  
> **Última actualización:** 2026-01-08

---

## Descripción

Módulo para gestionar todos los usuarios del sistema: crear, editar, asignar roles y activar/desactivar cuentas.

---

## Acceso

1. Iniciar sesión como **Super Admin** o **Manager**
2. Menú lateral → **Usuarios**

---

## Funcionalidades

### 1. Lista de Usuarios

| Columna | Descripción |
|---------|-------------|
| Nombre | Nombre completo del usuario |
| Email | Correo electrónico |
| Rol | Super Admin / Manager / Programador / Operador |
| Estado | Activo / Inactivo |
| Clientes | Cantidad de clientes asignados |
| Acciones | Ver, Editar, Desactivar |

### 2. Crear Usuario

**Ruta:** `/admin/users/create`

Campos:
- Nombre completo
- Email
- Contraseña
- Rol (selector)
- Estado activo (checkbox)

### 3. Editar Usuario

**Ruta:** `/admin/users/{id}/edit`

Permite modificar:
- Datos personales
- Rol asignado
- Estado activo/inactivo
- Reiniciar contraseña

### 4. Ver Detalle

**Ruta:** `/admin/users/{id}`

Muestra:
- Información del usuario
- Clientes asignados (si es Operador/Programador)
- Historial de actividad

---

## Rutas CRUD

| Acción | Ruta | Método |
|--------|------|--------|
| Listar | `/admin/users` | GET |
| Crear | `/admin/users/create` | GET |
| Guardar | `/admin/users` | POST |
| Ver | `/admin/users/{id}` | GET |
| Editar | `/admin/users/{id}/edit` | GET |
| Actualizar | `/admin/users/{id}` | PUT |
| Eliminar | `/admin/users/{id}` | DELETE |

---

## Roles del Sistema

| Rol | Descripción | Acceso Principal |
|-----|-------------|------------------|
| **Super Admin** | Control total del sistema | `/admin/*` |
| **Manager** | Administración sin config técnica | `/admin/*` |
| **Programador** | Crea y ejecuta workflows | `/programadores/*` |
| **Operador** | Gestiona sus clientes | `/clients` |

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| Controlador | `Admin\UserController` |
| Modelo | `User` |
| Sistema de Roles | Spatie Permission |

---

## Reglas de Negocio

- Un usuario debe tener exactamente un rol asignado
- Los usuarios inactivos no pueden iniciar sesión
- No se pueden eliminar usuarios con clientes o workflows asociados
- Cambiar contraseña requiere confirmación
