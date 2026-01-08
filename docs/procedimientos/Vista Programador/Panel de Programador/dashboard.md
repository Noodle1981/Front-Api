# Dashboard Programador

> **Ruta:** `/programadores/dashboard`  
> **Acceso:** Programador  
> **Última actualización:** 2026-01-08

---

## Descripción

Panel de control principal para programadores. Centro de supervisión de workflows, clientes y métricas operativas.

---

## Acceso

1. Iniciar sesión como **Programador**
2. Redirección automática desde `/dashboard`
3. O menú lateral → **Dashboard**

---

## Secciones de la Vista

### 1. Alerta de Estado del Sistema

Indicador visual del estado general:
- 🟢 **Sistema Operativo** - Todo funcionando correctamente
- 🟡 **Atención Requerida** - Hay workflows con errores
- 🔴 **Crítico** - Alto porcentaje de fallos

### 2. Métricas Principales (Cards)

| Métrica | Descripción |
|---------|-------------|
| **Clientes** | Total de clientes en el sistema |
| **Workflows Enviados** | Total histórico de batches procesados |
| **Informes PDF** | Workflows completados exitosamente |
| **Tasa de Éxito** | Porcentaje de ejecuciones exitosas |

### 3. Actividad Reciente

Lista de los últimos workflows ejecutados:
- Cliente y sucursal
- Tipo de workflow
- Fecha/hora de ejecución
- Estado (Completado/Error)
- Link a Preview PDF

### 4. Accesos Rápidos

Navegación directa a:
- **Cargar Workflow** → `/programadores/workflows/upload`
- **Historial** → `/programadores/workflows/history`
- **Clientes** → `/programadores/clients`

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| Controlador | `ProgrammerDashboardController@index` |
| Vista | `programmer/dashboard.blade.php` |
| Layout | `x-app-layout` |
| Middleware | `auth`, `role:Programador` |

---

## Datos del Controlador

```php
// ProgrammerDashboardController@index
$stats = [
    'clients' => Client::count(),
    'workflows_sent' => WorkflowFileBatch::count(),
    'pdf_reports' => WorkflowExecution::where('status', 'completed')->count(),
    'success_rate' => // Calculado dinámicamente
];

$recentActivity = WorkflowExecution::with('batch.client')
    ->latest()
    ->take(5)
    ->get();
```

---

## Navegación Disponible

| Destino | Ruta |
|---------|------|
| Dashboard | `/programadores/dashboard` |
| Cargar Workflows | `/programadores/workflows/upload` |
| Historial | `/programadores/workflows/history` |
| Clientes | `/programadores/clients` |
| Perfil | `/profile` |
