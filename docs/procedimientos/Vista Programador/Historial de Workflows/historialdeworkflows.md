# Historial de Workflows

> **Ruta:** `/programadores/workflows/history`  
> **Acceso:** Programador, Super Admin

---

## Descripción

Centro de gestión de todos los workflows ejecutados. Permite auditar resultados, ver batches y descargar reportes PDF.

---

## Acceso

1. Iniciar sesión como **Programador**
2. Menú lateral → **Historial de Workflows**

---

## Funcionalidades

### 1. Tabla de Ejecuciones

| Columna | Descripción |
|---------|-------------|
| Cliente | Nombre del cliente |
| Sucursal | Sede/sucursal procesada |
| Tipo | Tipo de workflow (ej: Conciliación) |
| Fecha | Fecha de ejecución |
| Estado | Pendiente / Completado / Error |
| Acciones | Ver Batch, Preview PDF, Descargar PDF |

### 2. Ver Batch

- **Ruta:** `/programadores/workflows/batch/{id}`
- Muestra el detalle del batch de archivos cargados
- Lista archivos procesados con sus metadatos
- Estado de validación de cada archivo

### 3. Preview PDF

- **Ruta:** `/programadores/workflows/execution/{id}/pdf/preview`
- Vista previa del reporte en navegador
- Muestra el PDF completo antes de descargar
- Botón "Descargar PDF" disponible

### 4. Descargar PDF

- **Ruta:** `/programadores/workflows/execution/{id}/pdf/download`
- Descarga directa del archivo PDF
- Nombre formato: `arqueo_caja_{fecha}_{sucursal}.pdf`

---

## Flujo de Uso

```
Historial → Seleccionar Ejecución → Ver Batch (opcional)
                                  → Preview PDF → Descargar PDF
```

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| Componente Livewire | `WorkflowHistoryTable` |
| Controlador Batch | `WorkflowBatchController@show` |
| Controlador PDF | `WorkflowPdfController@preview/download` |

---

## Tablas Relacionadas

- `workflow_file_batches` - Batches de archivos
- `workflow_executions` - Ejecuciones de workflows
- `workflow_uploaded_files` - Archivos individuales
