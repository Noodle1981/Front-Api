# Solicitar Workflows (Operador)

> **Estado:** ✅ IMPLEMENTADO (Sprint 4)  
> **Última actualización:** 2026-01-06

## Descripción

Formulario para que operadores soliciten la creación de nuevos workflows al equipo de programación. Permite especificar cliente, tipo, descripción y prioridad.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/operador/workflows/request` |
| **Ruta nombrada** | `operator.workflows.request` |
| **Controlador** | `App\Http\Controllers\Operator\WorkflowRequestController` |
| **Vista** | `resources/views/operator/workflows/request.blade.php` |
| **Layout** | `layouts/app` (via x-app-layout) |
| **Middleware** | `auth`, `role:Operador` |

---

## Rutas

| Método | Ruta | Nombre | Acción |
|--------|------|--------|--------|
| GET | `/operador/workflows/request` | `operator.workflows.request` | Mostrar formulario |
| POST | `/operador/workflows/request` | `operator.workflows.request.store` | Enviar solicitud |

---

## Campos del Formulario

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `client_id` | select | ✅ | Cliente para el workflow |
| `workflow_type` | select | ✅ | Tipo (Conciliación, Facturación, etc.) |
| `title` | text | ✅ | Título descriptivo |
| `description` | textarea | ✅ | Descripción detallada |
| `priority` | radio | ✅ | Baja / Media / Alta |
| `expected_date` | date | ❌ | Fecha esperada de entrega |

---

## Tipos de Workflow Disponibles

| Valor | Etiqueta |
|-------|----------|
| `conciliacion` | Conciliación Bancaria |
| `facturacion` | Facturación |
| `reportes` | Generación de Reportes |
| `importacion` | Importación de Datos |
| `otro` | Otro |

---

## Validación (Controller)

```php
$validated = $request->validate([
    'client_id' => 'required|exists:clients,id',
    'workflow_type' => 'required|string|max:50',
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'priority' => 'required|in:low,medium,high',
    'expected_date' => 'nullable|date|after:today',
]);
```

---

## Estado Actual

⚠️ **Placeholder**: El controlador actualmente:
- Valida los datos
- Redirige con mensaje de éxito
- **NO guarda en base de datos** (falta crear tabla `workflow_requests`)

---

## Implementación Futura

Para completar la funcionalidad:

1. **Crear migración:**
```php
Schema::create('workflow_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('client_id')->constrained();
    $table->string('workflow_type');
    $table->string('title');
    $table->text('description');
    $table->enum('priority', ['low', 'medium', 'high']);
    $table->date('expected_date')->nullable();
    $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected']);
    $table->timestamps();
});
```

2. **Crear modelo `WorkflowRequest`**
3. **Vista para programadores** para ver y gestionar solicitudes
4. **Notificaciones** al programador cuando llega solicitud

---

## Permisos Requeridos

- Rol: `Operador`
- Solo puede seleccionar sus clientes asignados
- Acceso: Menú lateral "Solicitar Workflow"