# Dashboard Programador

> **Estado:** ✅ OK (Limpiado en Sprint 1)  
> **Última actualización:** 2026-01-06

## Descripción

Panel de control principal para los programadores. Ofrece una visión del rendimiento del sistema, actividad de los operadores y accesos directos a las herramientas de desarrollo (Carga de Workflows, Reglas de Negocio, etc.).

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/dashboard` |
| **Ruta nombrada** | `programmer.dashboard` |
| **Controlador** | `App\Http\Controllers\ProgrammerDashboardController@index` |
| **Vista** | `resources/views/programmer/dashboard.blade.php` |
| **Layout** | `layouts/programmer` |
| **Middleware** | `auth`, `role:Programador` |

---

## Secciones de la Vista

### 1. Métricas de Rendimiento
- Resumen de ejecuciones exitosas vs. fallidos.
- Carga de trabajo actual.

### 2. Actividad de Operadores
- Vista detallada de la productividad de los operadores asignados.

---

## Datos del Controlador

El controlador recopila datos específicos para el rol de programador:

```php
// ProgrammerDashboardController@index
$stats = [
    'totalWorkflows' => WorkflowFileBatch::count(),
    'successRate' => CalculateSuccessRate(),
    // ...
];
```

---

## Permisos Requeridos

- Rol: `Programador`
- Acceso: Redirección automática desde `/dashboard` para usuarios con este rol.

---

## Notas

- En Sprint 1 se eliminaron todas las referencias a "automatización" de este dashboard.
- Se quitaron rankings y columnas de porcentajes de automatización para simplificar la vista.