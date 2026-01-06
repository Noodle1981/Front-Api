# Mantenimiento del Sistema

> **Estado:** ✅ REASIGNADO (Sprint 2)  
> **Última actualización:** 2026-01-06

## Descripción

Herramientas de mantenimiento técnico del sistema. Estas funciones fueron movidas del rol de Administrador al de Programador para asegurar que solo personal técnico realice tareas críticas de infraestructura.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/maintenance` |
| **Ruta nombrada** | `programmer.maintenance` |
| **Controlador** | `App\Http\Controllers\Admin\MaintenanceController` |
| **Vista** | `resources/views/admin/maintenance.blade.php` |
| **Layout** | `layouts/programmer` |
| **Middleware** | `auth`, `role:Programador\|Super Admin` |

---

## Tareas de Mantenimiento

| Acción | Ruta | Descripción |
|--------|------|-------------|
| **Optimizar** | `programmer.maintenance.optimize` | Limpia caché de rutas, config y eventos. |
| **Caché** | `programmer.maintenance.clear-cache` | Limpia la caché de la aplicación. |
| **Vistas** | `programmer.maintenance.clear-views` | Limpia las vistas compiladas de Blade. |
| **Backup** | `programmer.maintenance.backup` | Genera un respaldo de la base de datos. |
| **Logs** | `programmer.maintenance.clean-logs` | Elimina archivos de log antiguos. |
| **Sesiones** | `programmer.maintenance.clean-sessions` | Limpia las sesiones activas del servidor. |

---

## Permisos Requeridos

- Rol: `Programador` o `Super Admin`.
- Acceso: Sección "Sistema" -> "Mantenimiento".
