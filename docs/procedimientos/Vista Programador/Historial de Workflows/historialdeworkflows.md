# Historial de Workflows (Programador)

> **Estado:** 🔧 MEJORAR (Sprint 3)  
> **Última actualización:** 2026-01-06

## Descripción

Centro de gestión de todos los workflows ejecutados en el sistema. Permite a los programadores auditar los resultados, ver batches y descargar reportes PDF.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/workflows/history` |
| **Ruta nombrada** | `programmer.workflows.history` |
| **Controlador** | `App\Http\Controllers\WorkflowHistoryController@index` |
| **Componente Livewire** | `App\Livewire\WorkflowHistoryTable` |
| **Vista** | `resources/views/programmer/workflows/history.blade.php` |
| **Layout** | `layouts/programmer` |
| **Middleware** | `auth`, `role:Programador\|Super Admin` |

---

## Funcionalidades

- **Auditoría:** Revisión línea por línea de los resultados procesados en cada lote (batch).
- **Reportes:** Generación de PDF con el resumen de la ejecución.
- **Preview del PDF:** (Actualmente en proceso de mejora debido a problemas de visualización).

---

## Modelo Relacionado

**Tabla:** `workflow_file_batches`
**Tabla Relacionada:** `workflow_file_results`

---

## Permisos Requeridos

- Rol: `Programador` o `Super Admin`.
- Acceso: Menú "Historial de Workflows".