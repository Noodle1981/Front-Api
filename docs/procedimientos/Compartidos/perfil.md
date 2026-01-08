# Perfil de Usuario

> **Ruta:** `/profile`  
> **Acceso:** Todos los usuarios autenticados  
> **Última actualización:** 2026-01-08

---

## Descripción

Página para que cualquier usuario del sistema pueda ver y editar su información personal.

---

## Acceso

1. Iniciar sesión con cualquier rol
2. Click en nombre de usuario (esquina superior derecha)
3. Seleccionar **"Perfil"**

---

## Funcionalidades

### 1. Información del Perfil

Datos que se pueden editar:
- Nombre completo
- Email

### 2. Actualizar Contraseña

Cambiar la contraseña actual:
- Contraseña actual (requerida para confirmar)
- Nueva contraseña
- Confirmar nueva contraseña

### 3. Eliminar Cuenta

Opción para eliminar permanentemente la cuenta:
- Requiere confirmación con contraseña
- Acción irreversible

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| Controlador | `ProfileController` |
| Vista | `profile/edit.blade.php` |
| Middleware | `auth` |
| Origen | Laravel Breeze |

---

## Rutas

| Acción | Ruta | Método |
|--------|------|--------|
| Ver/Editar | `/profile` | GET |
| Actualizar Datos | `/profile` | PATCH |
| Eliminar Cuenta | `/profile` | DELETE |

---

## Notas

- Esta es una ruta compartida para todos los roles
- No requiere permisos especiales, solo estar autenticado
- Provista por **Laravel Breeze** (autenticación)

