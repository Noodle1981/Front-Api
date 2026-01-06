# Monitor API (Programador)

> **Estado:** 🧹 LIMPIAR (Sprint 1)  
> **Última actualización:** 2026-01-06

## Descripción

Panel de monitoreo de las conexiones externas. Permite supervisar el estado de los servicios API integrados y diagnosticar fallas de comunicación.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/api-dashboard` |
| **Ruta nombrada** | `programmer.api-dashboard` |
| **Controlador** | `App\Http\Controllers\Admin\ApiDashboardController@index` (compartido/redirigido) |
| **Vista** | `resources/views/admin/api-dashboard.blade.php` |
| **Layout** | `layouts/programmer` (cuando se accede como programador) |
| **Middleware** | `auth`, `role:Programador` |

---

## Funcionalidades

- **Estado de Conexión:** Indicadores de disponibilidad de APIs.
- **Estadísticas de Tráfico:** (En Sprint 1 se eliminaron las estadísticas de automatización para centrarse en conectividad).

---

## Permisos Requeridos

- Rol: `Programador`.
- Acceso: Menú "Monitor API".