# Dashboard Operador

> **Estado:** ✅ OK (Limpiado en Sprint 1)  
> **Última actualización:** 2026-01-06

## Descripción

Panel principal para operadores. Muestra un centro de comando con información relevante del sistema y acceso rápido a las funcionalidades principales.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/dashboard` |
| **Ruta nombrada** | `dashboard` |
| **Controlador** | `App\Http\Controllers\DashboardController@index` |
| **Vista** | `resources/views/dashboard.blade.php` |
| **Layout** | `layouts/app` |
| **Middleware** | `auth`, `verified` |

---

## Secciones de la Vista

### 1. Resumen de Actividad
- Workflows recientes ejecutados
- Estado general del sistema

### 2. Accesos Rápidos
- Clientes
- Historial de Workflows
- Solicitar Workflow

### 3. Estadísticas Básicas
- Total de clientes asignados
- Workflows del mes

---

## Datos del Controlador

```php
// DashboardController@index
return view('dashboard', [
    'recentWorkflows' => $user->workflowBatches()->latest()->take(5)->get(),
    'clientCount' => $user->clients()->count(),
    // ...
]);
```

---

## Permisos Requeridos

- Rol: `Operador` (también accesible por otros roles, redirige según rol)
- Acceso: Autenticación requerida

---

## Notas

- Los administradores y programadores son redirigidos a sus dashboards específicos
- Las referencias a "automatización" fueron eliminadas en Sprint 1