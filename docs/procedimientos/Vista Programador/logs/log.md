# Logs del Sistema

> **Estado:** ✅ REASIGNADO (Sprint 2)  
> **Última actualización:** 2026-01-06

## Descripción

Visor de logs de Laravel para propósitos de depuración y monitoreo de errores. Movido al rol de Programador para facilitar el diagnóstico técnico sin intervención del administrador.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/system-logs` |
| **Ruta nombrada** | `programmer.logs` |
| **Controlador** | `App\Http\Controllers\Admin\DashboardController@logs` |
| **Vista** | `resources/views/admin/logs.blade.php` |
| **Layout** | `layouts/programmer` |
| **Middleware** | `auth`, `role:Programador\|Super Admin` |

---

## Funcionalidades

- **Lectura de Logs:** Visualización de los últimos registros de error (`storage/logs/laravel.log`).
- **Filtrado:** Permite identificar rápidamente errores críticos y advertencias.

---

## Permisos Requeridos

- Rol: `Programador` o `Super Admin`.
- Acceso: Sección "Sistema" -> "Logs".
