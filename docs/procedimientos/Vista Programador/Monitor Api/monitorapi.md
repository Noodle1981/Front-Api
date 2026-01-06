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
| **Vista** | `resources/views/programmer/api-dashboard.blade.php` |
| **Layout** | `layouts/programmer` (cuando se accede como programador) |
| **Middleware** | `auth`, `role:Programador` |

---

## Funcionalidades

- **Estado de Conexión:** Indicadores de disponibilidad de APIs (Errores hoy, Syncs hoy, Clientes conectados).
- **Métricas Avanzadas (Próximamente):** Sección preparada para futuras integraciones de latencia y carga de servidor.
- **Bitácora en Vivo:** Registro detallado de eventos y logs de APIs para depuración técnica.

---

## Permisos Requeridos

- Rol: `Programador`.
- Acceso: Menú "Monitor API".