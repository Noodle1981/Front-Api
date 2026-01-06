# Historial de Workflows (Operador)

> **Estado:** ✅ IMPLEMENTADO (Sprint 4)  
> **Última actualización:** 2026-01-06

## Descripción

Vista para que operadores vean el historial de ejecuciones de workflows de sus clientes. Reutiliza el componente del programador pero filtrado por los clientes asignados al operador.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/operador/workflows/history` |
| **Ruta nombrada** | `operator.workflows.history` |
| **Componente Livewire** | `App\Livewire\WorkflowHistoryTable` |
| **Vista** | Componente Livewire (auto-renderiza) |
| **Layout** | `layouts/app` |
| **Middleware** | `auth`, `role:Operador` |

---

## Funcionalidades

- 📋 **Lista de ejecuciones** - Tabla paginada con filtros
- 🔍 **Búsqueda** - Por cliente, fecha, estado
- 📄 **Ver detalle** - Acceso al batch específico
- 📥 **Descargar PDF** - Informe de ejecución

---

## Componente Livewire

El componente `WorkflowHistoryTable` muestra:

```php
// Filtrado automático por rol
if (auth()->user()->hasRole('Operador')) {
    $query->whereHas('client', function ($q) {
        $q->where('user_id', auth()->id());
    });
}
```

---

## Datos Mostrados

| Columna | Descripción |
|---------|-------------|
| Cliente | Nombre de la empresa |
| Tipo | Tipo de workflow ejecutado |
| Archivos | Cantidad de archivos procesados |
| Estado | Completado, En proceso, Error |
| Fecha | Fecha de ejecución |
| Acciones | Ver detalle, PDF |

---

## Permisos Requeridos

- Rol: `Operador`
- Solo ve workflows de sus clientes asignados
- Acceso: Menú lateral "Historial Workflows"

---

## Notas

- Esta funcionalidad fue agregada en Sprint 4
- Reutiliza el componente del programador con filtrado por rol