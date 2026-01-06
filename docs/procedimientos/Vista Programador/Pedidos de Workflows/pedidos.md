# Gestión de Pedidos de Workflows (Programador)

> **Estado:** ✅ IMPLEMENTADO (Sprint 4)  
> **Última actualización:** 2026-01-06

## Descripción

Vista para que los programadores gestionen las solicitudes de workflows enviadas por los operadores. Permite visualizar, aceptar o rechazar pedidos con información detallada sobre prioridad, cliente, tipo de workflow y fecha esperada.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programador/workflows/requests` |
| **Ruta nombrada** | `programmer.workflows.requests` |
| **Controlador** | `App\Http\Controllers\Programmer\WorkflowRequestController` |
| **Modelo** | `App\Models\WorkflowRequest` |
| **Vista** | `resources/views/programmer/workflows/requests.blade.php` |
| **Layout** | `layouts/app` (via x-app-layout) |
| **Middleware** | `auth`, `role:Programador` |

---

## Rutas

| Método | Ruta | Nombre | Acción |
|--------|------|--------|--------|
| GET | `/programador/workflows/requests` | `programmer.workflows.requests` | Listar pedidos |
| POST | `/programador/workflows/requests/{request}/accept` | `programmer.workflows.requests.accept` | Aceptar pedido |
| POST | `/programador/workflows/requests/{request}/reject` | `programmer.workflows.requests.reject` | Rechazar pedido |

---

## Funcionalidades Principales

### 1. Visualización de Pedidos

La vista muestra todos los pedidos de workflows con la siguiente información:

| Campo | Descripción |
|-------|-------------|
| **Título** | Nombre descriptivo del workflow solicitado |
| **Tipo de Workflow** | Badge con el tipo (Conciliación, Facturación, etc.) |
| **Prioridad** | Indicador visual con color (Rojo=Alta, Amarillo=Media, Verde=Baja) |
| **Operador** | Nombre del usuario que solicitó el workflow |
| **Cliente** | Empresa para la que se solicita el workflow |
| **Fecha de Solicitud** | Cuándo se creó el pedido |
| **Descripción** | Detalles del requerimiento |
| **Fecha Esperada** | (Opcional) Fecha límite esperada |
| **Estado** | Pendiente, Aceptado o Rechazado |

### 2. Indicadores Visuales de Prioridad

Los pedidos tienen un borde lateral coloreado según su prioridad:

- 🔴 **Rojo** (Alta): `border-red-500`
- 🟡 **Amarillo** (Media): `border-yellow-500`
- 🟢 **Verde** (Baja): `border-green-500`

Cada tarjeta también incluye un ícono de prioridad:
- Alta: `fa-arrow-up` (flecha arriba)
- Media: `fa-minus` (línea horizontal)
- Baja: `fa-arrow-down` (flecha abajo)

### 3. Acciones Disponibles

#### Aceptar Pedido
```php
POST /programador/workflows/requests/{request}/accept
```
- Cambia el estado a `accepted`
- Muestra mensaje de éxito
- Deshabilita los botones de acción

#### Rechazar Pedido
```php
POST /programador/workflows/requests/{request}/reject
```
- Cambia el estado a `rejected`
- Muestra mensaje de advertencia
- Deshabilita los botones de acción

### 4. Filtros y Búsqueda

La vista incluye controles para:
- 🔍 **Búsqueda por texto** (placeholder implementado)
- 📊 **Filtros por estado**: TODO, PENDIENTE, ACEPTADO
- 📈 **Contador de solicitudes** en el header

> ⚠️ **Nota**: Los filtros están implementados visualmente pero requieren funcionalidad JavaScript para ser operativos.

---

## Estructura del Controlador

### Método `index()`

```php
public function index()
{
    $requests = WorkflowRequest::with(['user', 'client'])
        ->latest()
        ->get();

    return view('programmer.workflows.requests', compact('requests'));
}
```

**Características:**
- Carga relaciones `user` y `client` con eager loading
- Ordena por más recientes primero
- Retorna todos los pedidos (sin paginación actualmente)

### Método `accept()`

```php
public function accept(WorkflowRequest $request)
{
    $request->update(['status' => 'accepted']);
    return back()->with('success', 'Pedido aceptado correctamente.');
}
```

**Características:**
- Usa route model binding
- Actualiza solo el campo `status`
- Redirige a la misma página con mensaje flash

### Método `reject()`

```php
public function reject(WorkflowRequest $request)
{
    $request->update(['status' => 'rejected']);
    return back()->with('warning', 'Pedido rechazado.');
}
```

**Características:**
- Similar a `accept()` pero con estado `rejected`
- Mensaje de tipo `warning` en lugar de `success`

---

## Modelo WorkflowRequest

### Campos Fillable

```php
protected $fillable = [
    'user_id',
    'client_id',
    'workflow_type',
    'title',
    'description',
    'priority',
    'expected_date',
    'status',
];
```

### Relaciones

#### `user()`
```php
public function user()
{
    return $this->belongsTo(User::class);
}
```
Retorna el operador que solicitó el workflow.

#### `client()`
```php
public function client()
{
    return $this->belongsTo(Client::class);
}
```
Retorna el cliente para el que se solicita el workflow.

---

## Esquema de Base de Datos

### Tabla: `workflow_requests`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | bigint | Primary key |
| `user_id` | bigint | FK a `users` (operador solicitante) |
| `client_id` | bigint | FK a `clients` |
| `workflow_type` | string | Tipo de workflow solicitado |
| `title` | string | Título del pedido |
| `description` | text | Descripción detallada |
| `priority` | enum | `low`, `medium`, `high` |
| `expected_date` | date | Fecha esperada (nullable) |
| `status` | enum | `pending`, `in_progress`, `completed`, `rejected` |
| `created_at` | timestamp | Fecha de creación |
| `updated_at` | timestamp | Última actualización |

### Constraints

- `user_id`: Foreign key con `onDelete('cascade')`
- `client_id`: Foreign key con `onDelete('cascade')`
- `priority`: Default `'medium'`
- `status`: Default `'pending'`

---

## Estados del Workflow Request

| Estado | Valor | Descripción |
|--------|-------|-------------|
| **Pendiente** | `pending` | Recién solicitado, esperando revisión |
| **En Progreso** | `in_progress` | Aceptado y en desarrollo (no usado actualmente) |
| **Completado** | `completed` | Workflow creado y entregado (no usado actualmente) |
| **Rechazado** | `rejected` | Pedido rechazado por el programador |

> 📝 **Nota**: Actualmente solo se usan los estados `pending`, `accepted` y `rejected`. Los estados `in_progress` y `completed` están disponibles para futuras implementaciones.

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

## Diseño y UX

### Componentes Visuales

1. **Header con contador**: Muestra el total de solicitudes
2. **Barra de filtros**: Búsqueda y filtros por estado
3. **Tarjetas de pedidos**: Diseño card con:
   - Borde lateral coloreado por prioridad
   - Ícono de prioridad
   - Badges de tipo y estado
   - Información del solicitante y cliente
   - Descripción en panel destacado
   - Botones de acción (Aceptar/Rechazar)

### Estados de los Botones

- **Pendiente**: Botones activos (Rechazar + Aceptar)
- **Procesado**: Botón deshabilitado con texto "Procesado"

### Mensajes Flash

La vista responde a los siguientes mensajes flash:
- `success`: Pedido aceptado correctamente
- `warning`: Pedido rechazado

---

## Mejoras Futuras

### Funcionalidad Pendiente

1. **Implementar búsqueda en tiempo real**
   - JavaScript para filtrar tarjetas por texto
   - Búsqueda en título, descripción, cliente

2. **Activar filtros por estado**
   - Filtrar por: TODO, PENDIENTE, ACEPTADO, RECHAZADO
   - Actualización dinámica sin recargar página

3. **Agregar paginación**
   - Descomentar `{{ $requests->links() }}`
   - Modificar controlador para usar `paginate()`

4. **Implementar estado "En Progreso"**
   - Botón adicional para marcar como "en desarrollo"
   - Vista de tracking de progreso

5. **Notificaciones**
   - Notificar al operador cuando su pedido es aceptado/rechazado
   - Notificar al programador cuando llega un nuevo pedido

6. **Comentarios y seguimiento**
   - Permitir al programador agregar comentarios
   - Historial de cambios de estado
   - Estimación de tiempo de desarrollo

7. **Asignación de programadores**
   - Campo `assigned_to` en la tabla
   - Vista de "Mis pedidos asignados"

8. **Métricas y reportes**
   - Tiempo promedio de respuesta
   - Pedidos por tipo de workflow
   - Tasa de aceptación/rechazo

---

## Integración con Otros Módulos

### Relación con Vista Operador

Los operadores crean pedidos desde:
- **Ruta**: `/operador/workflows/request`
- **Controlador**: `Operator\WorkflowRequestController`
- **Documentación**: Ver [solicitar.md](../../Vista%20Operador/Solicitar%20Workflows/solicitar.md)

### Flujo Completo

1. **Operador** completa formulario de solicitud
2. **Sistema** valida y guarda en `workflow_requests`
3. **Programador** recibe notificación (pendiente)
4. **Programador** revisa pedido en `/programador/workflows/requests`
5. **Programador** acepta o rechaza
6. **Operador** recibe notificación del resultado (pendiente)
7. Si aceptado: **Programador** desarrolla el workflow
8. Al completar: Cambiar estado a `completed` (pendiente)

---

## Permisos Requeridos

- **Rol**: `Programador`
- **Acceso**: Menú lateral "Pedidos de Workflows"
- **Acciones**: Ver todos los pedidos, aceptar, rechazar

---

## Testing

### Casos de Prueba Recomendados

1. ✅ Visualizar lista de pedidos vacía
2. ✅ Visualizar pedidos con diferentes prioridades
3. ✅ Aceptar un pedido pendiente
4. ✅ Rechazar un pedido pendiente
5. ⏳ Intentar aceptar un pedido ya procesado
6. ⏳ Filtrar por estado
7. ⏳ Buscar por texto
8. ⏳ Verificar eager loading de relaciones

### Datos de Prueba

```php
// Factory para crear pedidos de prueba
WorkflowRequest::factory()->create([
    'user_id' => 1, // Operador
    'client_id' => 1,
    'workflow_type' => 'conciliacion',
    'title' => 'Workflow de Conciliación Bancaria',
    'description' => 'Necesitamos automatizar la conciliación...',
    'priority' => 'high',
    'expected_date' => now()->addDays(7),
    'status' => 'pending',
]);
```

---

## Archivos Relacionados

### Controladores
- [`WorkflowRequestController.php`](file:///d:/Front-Api/app/Http/Controllers/Programmer/WorkflowRequestController.php) (Programador)
- [`WorkflowRequestController.php`](file:///d:/Front-Api/app/Http/Controllers/Operator/WorkflowRequestController.php) (Operador)

### Modelos
- [`WorkflowRequest.php`](file:///d:/Front-Api/app/Models/WorkflowRequest.php)

### Vistas
- [`requests.blade.php`](file:///d:/Front-Api/resources/views/programmer/workflows/requests.blade.php)

### Migraciones
- [`2026_01_06_113201_create_workflow_requests_table.php`](file:///d:/Front-Api/database/migrations/2026_01_06_113201_create_workflow_requests_table.php)

### Rutas
- [`web.php`](file:///d:/Front-Api/routes/web.php) (líneas 140-142)

---

## Notas de Implementación

### Decisiones de Diseño

1. **Sin paginación inicial**: Se decidió mostrar todos los pedidos para facilitar la revisión rápida
2. **Estados simplificados**: Solo se usan `pending`, `accepted`, `rejected` inicialmente
3. **Sin asignación**: Todos los programadores ven todos los pedidos
4. **Filtros visuales**: Implementados en UI pero sin funcionalidad backend

### Consideraciones de Rendimiento

- ⚠️ **Eager Loading**: Se usa `with(['user', 'client'])` para evitar N+1 queries
- ⚠️ **Sin paginación**: Puede ser problemático con muchos pedidos
- ✅ **Índices**: Foreign keys tienen índices automáticos

### Seguridad

- ✅ CSRF protection en formularios
- ✅ Route model binding previene inyección
- ✅ Middleware de autenticación y rol
- ⚠️ No hay validación de permisos adicionales (cualquier programador puede aceptar/rechazar)

---

## Changelog

| Fecha | Versión | Cambios |
|-------|---------|---------|
| 2026-01-06 | 1.0.0 | Implementación inicial con CRUD básico |

---

## Soporte

Para dudas o mejoras, contactar al equipo de desarrollo o revisar la documentación del proyecto en `/docs`.