# Dashboard Programador

> **Estado:** ✅ REFACTORIZADO (Nuevos Requerimientos)  
> **Última actualización:** 2026-01-06

## Descripción

Panel de control principal para los programadores (Centro de Comando). Se enfoca en la supervisión de procesos de workflows técnicos, informes generados y la gestión de solicitudes provenientes de los operadores.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/dashboard` |
| **Ruta nombrada** | `programmer.dashboard` |
| **Controlador** | `App\Http\Controllers\ProgrammerDashboardController@index` |
| **Vista** | `resources/views/programmer/dashboard.blade.php` |
| **Layout** | `x-app-layout` (con navegación lateral) |
| **Middleware** | `auth`, `role:Programador` |

---

## Secciones de la Vista

### 1. Estado del Sistema
- Basado en la tasa de fallo de los batches de workflows (`WorkflowFileBatch`).
- Indica si la tendencia de errores está mejorando o requiere atención.

### 2. Pedidos de Workflows
- Nueva sección que muestra solicitudes pendientes creadas por operadores (`workflow_requests`).
- Permite ver detalles de la solicitud y priorizar el desarrollo.

### 3. Métricas Principales (Cards)
- **Operadores:** Total de operadores registrados.
- **Clientes:** Total de clientes en el sistema.
- **Informes PDF:** Conteo de workflows completados exitosamente.
- **Workflows Enviados:** Total histórico de batches procesados.

### 4. Tabla de Operadores (Vista Simplificada)
- Lista de operadores con conteo de clientes asignados y fecha de última actividad de workflows.
- Se han eliminado las métricas de "Automatización" y "Carga" para alinearse con el nuevo modelo operativo.

---

## Datos del Controlador

El controlador recopila datos basados en la actividad técnica de los archivos:

```php
// ProgrammerDashboardController@index
$stats = [
    'workflows_sent' => WorkflowFileBatch::count(),
    'pdf_reports' => WorkflowFileBatch::where('status', 'completed')->count(), 
    'error_rate' => CalculateFailureRateFromBatches(),
];
```

---

## Permisos Requeridos

- Rol: `Programador`
- Acceso: Redirección automática desde `/dashboard`.

---

## Notas de Evolución

- **Sprint 1/4:** Eliminación total de referencias a "automatización" de APIs y rankings de eficiencia basados en automatización.
- **Gestión de Solicitudes:** Introducción de la tabla `workflow_requests` para centralizar la comunicación Operador -> Programador.