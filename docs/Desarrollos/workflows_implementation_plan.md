# Plan de Implementación: Sistema de Conciliación de Archivos

## Resumen Ejecutivo

Este plan propone la implementación de un sistema robusto para la carga, validación y procesamiento de archivos de conciliación financiera. El sistema permitirá a usuarios con rol "Programador" cargar múltiples archivos Excel, validar su estructura, generar JSON para envío a API externa, ejecutar workflows de negocio, y visualizar resultados. Los usuarios con rol "Operador" podrán consultar el historial y descargar reportes PDF.

## Contexto del Problema

El sistema actual tiene tablas básicas de `workflows` y `workflow_executions`, pero carece de:
- Definición de tipos de workflow (ej: "Conciliación")
- Sistema de carga y validación de archivos
- Vinculación entre archivos cargados y ejecuciones
- Almacenamiento estructurado de resultados
- Interfaz visual para seguimiento de progreso
- Generación de PDF desde historial

## User Review Required

> [!IMPORTANT]
> **Estructura JSON Confirmada**
> 
> El JSON que se enviará al servidor Python tendrá la siguiente estructura:
> ```json
> {
>   "Data": {
>     "Turnos": [
>       {"Fecha Apertura": "...", "Hs Ap. Caja": "...", ...},
>       {...}
>     ],
>     "Reporte_Ventas": [
>       {"FechaCierre": "...", "Comanda": "...", ...},
>       {...}
>     ],
>     "Reporte_getnet": [...],
>     "Prueba_MP": [...],
>     "Devoluciones": [...],
>     "Caja_Adicion": [...]
>   }
> }
> ```
> 
> El servidor procesará este JSON con lógica Python y devolverá un resultado que:
> - Se mostrará temporalmente en `/test` como JSON
> - Eventualmente se formateará en un PDF bonito

> [!IMPORTANT]
> **Compatibilidad con Sistema Existente**
> 
> Análisis de compatibilidad completado:
> - ✅ **Roles**: Ya existen (Spatie Permissions instalado) - No se crearán nuevos
> - ✅ **Clientes**: Tabla `clients` con soporte para sedes/anexos (`parent_id`, `branch_name`)
> - ✅ **API Logs**: Tabla `api_logs` existente - se puede reutilizar para logging
> - ✅ **Workflows**: Tablas base `workflows` y `workflow_executions` ya existen
> 
> **Decisión de Arquitectura:** Las nuevas tablas se integrarán perfectamente con la estructura existente.

---

## Cambios Propuestos

### 1. Migraciones de Base de Datos

#### Nueva Tabla: `workflow_types`

Define los tipos de workflow disponibles (ej: "Conciliación").

```php
Schema::create('workflow_types', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // "Conciliación"
    $table->string('slug')->unique(); // "conciliacion"
    $table->text('description')->nullable();
    $table->json('required_files')->comment('Estructura: [{name, columns: []}]');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

#### Nueva Tabla: `workflow_file_batches`

Almacena cada conjunto de archivos cargados.

```php
Schema::create('workflow_file_batches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained()->onDelete('cascade');
    $table->foreignId('branch_id')->nullable()->constrained('clients')->onDelete('cascade');
    $table->foreignId('workflow_type_id')->constrained()->onDelete('cascade');
    $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
    $table->string('status')->default('uploaded'); // uploaded, validated, processing, completed, failed
    $table->json('validation_results')->nullable(); // Checklist de validaciones
    $table->longText('generated_json')->nullable(); // JSON armado para enviar
    $table->longText('api_response')->nullable(); // Respuesta del servidor
    $table->timestamp('uploaded_at');
    $table->timestamp('processed_at')->nullable();
    $table->timestamps();
});
```

#### Nueva Tabla: `workflow_uploaded_files`

Detalle de cada archivo individual cargado.

```php
Schema::create('workflow_uploaded_files', function (Blueprint $table) {
    $table->id();
    $table->foreignId('batch_id')->constrained('workflow_file_batches')->onDelete('cascade');
    $table->string('expected_name'); // "Turnos", "Reporte_Ventas", etc.
    $table->string('uploaded_filename'); // Nombre real del archivo
    $table->string('file_path'); // Ruta en storage
    $table->integer('rows_count')->nullable();
    $table->json('columns_found')->nullable(); // Columnas detectadas
    $table->boolean('structure_valid')->default(false);
    $table->json('validation_errors')->nullable();
    $table->timestamps();
});
```

#### Modificación: Tabla `workflow_executions`

Agregar relación con el batch de archivos.

```php
Schema::table('workflow_executions', function (Blueprint $table) {
    $table->foreignId('file_batch_id')->nullable()->after('workflow_id')
        ->constrained('workflow_file_batches')->onDelete('cascade');
    $table->foreignId('executed_by')->nullable()->after('file_batch_id')
        ->constrained('users')->onDelete('set null');
    $table->longText('result_json')->nullable()->after('logs_json');
});
```

---

### 2. Modelos Eloquent

#### [NEW] [WorkflowType.php](file:///d:/Front-Api/app/Models/WorkflowType.php)

```php
class WorkflowType extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'required_files', 'is_active'];
    protected $casts = ['required_files' => 'array', 'is_active' => 'boolean'];
    
    public function batches() {
        return $this->hasMany(WorkflowFileBatch::class);
    }
}
```

#### [NEW] [WorkflowFileBatch.php](file:///d:/Front-Api/app/Models/WorkflowFileBatch.php)

```php
class WorkflowFileBatch extends Model
{
    protected $fillable = [...];
    protected $casts = [
        'validation_results' => 'array',
        'uploaded_at' => 'datetime',
        'processed_at' => 'datetime',
    ];
    
    public function client() { return $this->belongsTo(Client::class); }
    public function branch() { return $this->belongsTo(Client::class, 'branch_id'); }
    public function workflowType() { return $this->belongsTo(WorkflowType::class); }
    public function uploadedBy() { return $this->belongsTo(User::class, 'uploaded_by'); }
    public function files() { return $this->hasMany(WorkflowUploadedFile::class, 'batch_id'); }
    public function executions() { return $this->hasMany(WorkflowExecution::class, 'file_batch_id'); }
}
```

#### [NEW] [WorkflowUploadedFile.php](file:///d:/Front-Api/app/Models/WorkflowUploadedFile.php)

```php
class WorkflowUploadedFile extends Model
{
    protected $fillable = [...];
    protected $casts = [
        'columns_found' => 'array',
        'validation_errors' => 'array',
        'structure_valid' => 'boolean',
    ];
    
    public function batch() { return $this->belongsTo(WorkflowFileBatch::class); }
}
```

#### [MODIFY] [WorkflowExecution.php](file:///d:/Front-Api/app/Models/WorkflowExecution.php)

Agregar relaciones:

```php
public function fileBatch() { return $this->belongsTo(WorkflowFileBatch::class, 'file_batch_id'); }
public function executedBy() { return $this->belongsTo(User::class, 'executed_by'); }
```

---

### 3. Componentes Livewire

#### [NEW] [WorkflowFileUploadWizard.php](file:///d:/Front-Api/app/Livewire/WorkflowFileUploadWizard.php)

Wizard de 4 pasos:
1. Selección de Cliente y Sede
2. Selección de Workflow Type (ej: "Conciliación")
3. Carga de archivos con validación en tiempo real
4. Revisión y confirmación

**Características:**
- Validación de nombres de archivo
- Validación de estructura de columnas usando Laravel Excel
- Barra de progreso visual
- Checklist de validaciones (✓ nombres correctos, ✓ cantidad correcta, ✓ estructura válida)
- Generación de JSON preview

#### [NEW] [WorkflowExecutionPanel.php](file:///d:/Front-Api/app/Livewire/WorkflowExecutionPanel.php)

Panel para ejecutar workflow sobre un batch cargado:
- Muestra resumen del batch
- Botón "Ejecutar Workflow"
- Indicador de progreso durante ejecución
- Visualización de resultado

#### [NEW] [WorkflowHistoryTable.php](file:///d:/Front-Api/app/Livewire/WorkflowHistoryTable.php)

Tabla con historial de ejecuciones:
- Filtros por cliente, fecha, estado
- Columnas: Fecha, Cliente, Sede, Workflow, Usuario, Estado, Acciones
- Botón "Ver Detalle" y "Descargar PDF"
- Accesible para roles Programador y Operador

---

### 4. Servicios

#### [NEW] [FileValidationService.php](file:///d:/Front-Api/app/Services/FileValidationService.php)

Servicio para validar archivos Excel:

```php
class FileValidationService
{
    public function validateBatch(WorkflowFileBatch $batch): array
    {
        // 1. Verificar cantidad de archivos
        // 2. Verificar nombres de archivos
        // 3. Validar estructura de cada archivo
        // 4. Retornar checklist de validaciones
    }
    
    public function validateFileStructure(UploadedFile $file, array $expectedColumns): array
    {
        // Usar Laravel Excel para leer headers
        // Comparar con columnas esperadas
        // Retornar errores si hay discrepancias
    }
}
```

#### [NEW] [WorkflowJsonGeneratorService.php](file:///d:/Front-Api/app/Services/WorkflowJsonGeneratorService.php)

Genera el JSON consolidado con la estructura `{Data: {archivo: campos}}`:

```php
class WorkflowJsonGeneratorService
{
    public function generateFromBatch(WorkflowFileBatch $batch): array
    {
        $data = [];
        
        foreach ($batch->files as $uploadedFile) {
            // Leer archivo Excel
            $rows = Excel::toArray(new GenericImport, storage_path('app/' . $uploadedFile->file_path));
            
            // Usar el nombre esperado como key (sin extensión)
            $fileName = $uploadedFile->expected_name;
            $data[$fileName] = $rows[0]; // Primera hoja del Excel
        }
        
        return [
            'Data' => $data,
            'metadata' => [
                'client_id' => $batch->client_id,
                'branch_id' => $batch->branch_id,
                'uploaded_at' => $batch->uploaded_at->toIso8601String(),
                'workflow_type' => $batch->workflowType->name
            ]
        ];
    }
}
```

#### [NEW] [WorkflowExecutionService.php](file:///d:/Front-Api/app/Services/WorkflowExecutionService.php)

Ejecuta el workflow y maneja la comunicación con el servidor Python:

```php
class WorkflowExecutionService
{
    protected $jsonGenerator;
    
    public function __construct(WorkflowJsonGeneratorService $jsonGenerator)
    {
        $this->jsonGenerator = $jsonGenerator;
    }
    
    public function execute(WorkflowFileBatch $batch, User $user): WorkflowExecution
    {
        // 1. Crear registro de ejecución
        $execution = WorkflowExecution::create([
            'workflow_id' => $batch->workflowType->id,
            'file_batch_id' => $batch->id,
            'executed_by' => $user->id,
            'status' => 'running',
            'started_at' => now(),
        ]);
        
        try {
            // 2. Generar JSON
            $jsonData = $this->jsonGenerator->generateFromBatch($batch);
            $batch->update(['generated_json' => json_encode($jsonData)]);
            
            // 3. Enviar a API externa (o mock)
            if (config('workflows.use_mock_api', true)) {
                $response = $this->mockApiResponse($jsonData);
            } else {
                $response = $this->callExternalApi($jsonData);
            }
            
            // 4. Guardar respuesta
            $batch->update([
                'api_response' => json_encode($response),
                'status' => 'completed',
                'processed_at' => now()
            ]);
            
            $execution->update([
                'status' => 'completed',
                'result_json' => json_encode($response),
                'completed_at' => now(),
                'logs_json' => ['message' => 'Ejecución exitosa']
            ]);
            
            // 5. Log en api_logs
            ApiLog::create([
                'client_id' => $batch->client_id,
                'api_service_id' => 1, // ID del servicio de conciliación
                'status' => 'success',
                'event_type' => 'Workflow Execution',
                'details' => "Workflow {$batch->workflowType->name} ejecutado exitosamente",
                'happened_at' => now()
            ]);
            
        } catch (\Exception $e) {
            $execution->update([
                'status' => 'failed',
                'completed_at' => now(),
                'logs_json' => ['error' => $e->getMessage()]
            ]);
            
            $batch->update(['status' => 'failed']);
            
            throw $e;
        }
        
        return $execution->fresh();
    }
    
    protected function mockApiResponse(array $jsonData): array
    {
        // Simular respuesta del servidor Python
        return [
            'status' => 'success',
            'result' => [
                'total_ventas' => 125000.50,
                'total_efectivo' => 45000.00,
                'total_getnet' => 50000.50,
                'total_mp' => 30000.00,
                'diferencias' => [
                    ['tipo' => 'Descuadre en turno 1', 'monto' => -150.00],
                    ['tipo' => 'Faltante Getnet', 'monto' => -50.50]
                ],
                'observaciones' => 'Conciliación completada con 2 diferencias menores'
            ],
            'processed_at' => now()->toIso8601String()
        ];
    }
    
    protected function callExternalApi(array $jsonData): array
    {
        $response = Http::timeout(60)
            ->post(config('workflows.api_endpoint'), $jsonData);
            
        if (!$response->successful()) {
            throw new \Exception("API Error: " . $response->body());
        }
        
        return $response->json();
    }
}
```

#### [NEW] [WorkflowPdfService.php](file:///d:/Front-Api/app/Services/WorkflowPdfService.php)

Genera PDF del historial usando DomPDF o similar:

```php
class WorkflowPdfService
{
    public function generateExecutionReport(WorkflowExecution $execution): string
    {
        // Generar PDF con:
        // - Datos del cliente y sede
        // - Archivos procesados
        // - Resultado del workflow
        // - Timestamp y usuario
        // Retornar path del PDF generado
    }
}
```

---

### 5. Rutas y Controladores

#### [NEW] Rutas en `web.php`

```php
Route::middleware(['auth'])->group(function () {
    // Solo Programador
    Route::middleware(['role:Programador'])->group(function () {
        Route::get('/workflows/upload', WorkflowFileUploadWizard::class)->name('workflows.upload');
        Route::get('/workflows/execute/{batch}', WorkflowExecutionPanel::class)->name('workflows.execute');
    });
    
    // Programador y Operador
    Route::middleware(['role:Programador|Operador'])->group(function () {
        Route::get('/workflows/history', WorkflowHistoryTable::class)->name('workflows.history');
        Route::get('/workflows/history/{execution}/pdf', [WorkflowController::class, 'downloadPdf'])
            ->name('workflows.history.pdf');
    });
    
    // Test endpoint (solo desarrollo)
    Route::get('/test.html', [WorkflowController::class, 'testView'])->name('workflows.test');
});
```

#### [NEW] [WorkflowController.php](file:///d:/Front-Api/app/Http/Controllers/WorkflowController.php)

```php
class WorkflowController extends Controller
{
    public function downloadPdf(WorkflowExecution $execution)
    {
        $pdfService = new WorkflowPdfService();
        $path = $pdfService->generateExecutionReport($execution);
        return response()->download($path);
    }
    
    public function testView()
    {
        // Mostrar últimos JSON generados para debugging
        $batches = WorkflowFileBatch::latest()->take(10)->get();
        return view('workflows.test', compact('batches'));
    }
}
```

---

### 6. Vistas Blade

#### [NEW] [test.blade.php](file:///d:/Front-Api/resources/views/workflows/test.blade.php)

Vista para visualizar JSON generados y respuestas del servidor:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Test - Workflow JSON Viewer</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .batch-container { margin-bottom: 40px; border: 1px solid #444; padding: 20px; border-radius: 8px; }
        .batch-header { color: #4ec9b0; font-size: 18px; margin-bottom: 10px; }
        .json-section { margin-top: 15px; }
        .json-title { color: #dcdcaa; font-weight: bold; margin-bottom: 5px; }
        pre { background: #252526; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .status { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
        .status.completed { background: #4ec9b0; color: #000; }
        .status.failed { background: #f48771; color: #000; }
    </style>
</head>
<body>
    <h1>🔍 Workflow JSON Test Viewer</h1>
    
    @forelse($batches as $batch)
        <div class="batch-container">
            <div class="batch-header">
                Batch #{{ $batch->id }} - {{ $batch->client->company }}
                @if($batch->branch)
                    ({{ $batch->branch->branch_name }})
                @endif
                <span class="status {{ $batch->status }}">{{ strtoupper($batch->status) }}</span>
            </div>
            
            <div><strong>Workflow:</strong> {{ $batch->workflowType->name }}</div>
            <div><strong>Subido por:</strong> {{ $batch->uploadedBy->name }}</div>
            <div><strong>Fecha:</strong> {{ $batch->uploaded_at->format('d/m/Y H:i:s') }}</div>
            
            @if($batch->generated_json)
                <div class="json-section">
                    <div class="json-title">📤 JSON Enviado al Servidor:</div>
                    <pre>{{ json_encode(json_decode($batch->generated_json), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
            
            @if($batch->api_response)
                <div class="json-section">
                    <div class="json-title">📥 Respuesta del Servidor Python:</div>
                    <pre>{{ json_encode(json_decode($batch->api_response), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    @empty
        <p>No hay batches procesados aún.</p>
    @endforelse
</body>
</html>
```

---

### 7. Seeders

#### [NEW] [WorkflowTypeSeeder.php](file:///d:/Front-Api/database/seeders/WorkflowTypeSeeder.php)

Crear el workflow type "Conciliación" con su estructura de archivos:

```php
WorkflowType::create([
    'name' => 'Conciliación',
    'slug' => 'conciliacion',
    'description' => 'Conciliación de datos financieros con múltiples fuentes',
    'required_files' => [
        [
            'name' => 'Turnos',
            'columns' => ['Fecha Apertura', 'Hs Ap. Caja', 'Fecha Cierre', 'Hs Cierre Caja', 'TURNO', 'Encargado', 'APERTURA CAJA Efectivo', 'Recuento Efectivo']
        ],
        [
            'name' => 'Reporte_Ventas',
            'columns' => ['FechaCierre', 'Comanda', 'Total', 'Propina', 'Pagos', 'Boleta', 'Efectivo', 'Getnet', 'Mercado Pago', 'Cta Cte']
        ],
        [
            'name' => 'Reporte_getnet',
            'columns' => ['Fecha de operacion', 'Cod de Transaccion', 'Monto Bruto Transaccion', 'Arancel', 'Estado']
        ],
        [
            'name' => 'Prueba_MP',
            'columns' => ['FECHA DE ORIGEN (ISO)', 'ID DE OPERACIÓN EN MERCADO PAGO', 'VALOR DE LA COMPRA', 'MEDIO DE PAGO', 'NÚMERO DE SERIE DEL LECTOR']
        ],
        [
            'name' => 'Devoluciones',
            'columns' => ['ID Comanda', 'Producto', 'Precios', 'Hora pedido', 'Hora Anulación', 'Descuadre', 'DTE Emision']
        ],
        [
            'name' => 'Caja_Adicion',
            'columns' => ['Fecha Contable', 'Origen', 'Proveedor / Para', 'Monto', 'Forma de Pago', 'Comentario Toteat POS']
        ]
    ],
    'is_active' => true
]);
```

---

## Plan de Verificación

### Tests Automatizados

#### 1. Test de Validación de Archivos

```bash
php artisan test --filter FileValidationServiceTest
```

**Qué verifica:**
- Validación de nombres de archivo correctos/incorrectos
- Validación de estructura de columnas
- Detección de archivos faltantes
- Detección de archivos extra

#### 2. Test de Generación de JSON

```bash
php artisan test --filter WorkflowJsonGeneratorServiceTest
```

**Qué verifica:**
- JSON generado tiene estructura `{Data: {archivo: [rows]}}`
- Todos los 6 archivos están incluidos en el objeto `Data`
- Nombres de archivos como keys: "Turnos", "Reporte_Ventas", etc.
- Metadata incluye `client_id`, `branch_id`, `uploaded_at`, `workflow_type`
- Cada archivo contiene array de objetos con las columnas correctas

#### 3. Test de Permisos

```bash
php artisan test --filter WorkflowPermissionsTest
```

**Qué verifica:**
- Programador puede acceder a upload y execute
- Operador puede acceder a history pero no a upload
- Usuario sin rol no puede acceder

### Verificación Manual

#### 1. Flujo Completo de Carga

**Pasos:**
1. Iniciar sesión como usuario con rol "Programador"
2. Navegar a `/workflows/upload`
3. Seleccionar un cliente y sede
4. Seleccionar workflow "Conciliación"
5. Cargar los 6 archivos Excel (usar archivos de prueba en `storage/app/test-files/`)
6. Verificar que aparezcan checkmarks verdes para:
   - ✓ Cantidad de archivos correcta (6/6)
   - ✓ Nombres de archivos correctos
   - ✓ Estructura de columnas válida
7. Hacer clic en "Confirmar Carga"
8. Verificar que se cree el registro en `workflow_file_batches`

**Resultado esperado:** Batch creado con status "validated"

#### 2. Ejecución de Workflow

**Pasos:**
1. Desde el historial, hacer clic en "Ejecutar" sobre un batch cargado
2. Navegar a `/workflows/execute/{batch_id}`
3. Hacer clic en "Ejecutar Workflow"
4. Observar indicador de progreso
5. Navegar a `/test.html`
6. Verificar que aparezca el JSON generado

**Resultado esperado:** 
- JSON visible en `/test.html`
- Ejecución creada con status "completed"

#### 3. Descarga de PDF

**Pasos:**
1. Iniciar sesión como "Operador"
2. Navegar a `/workflows/history`
3. Hacer clic en "Descargar PDF" sobre una ejecución completada
4. Verificar que se descargue el PDF
5. Abrir PDF y verificar contenido

**Resultado esperado:** PDF descargado con información completa

---

## Consideraciones Técnicas

### Archivo de Configuración

Crear `config/workflows.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Endpoint del Servidor Python
    |--------------------------------------------------------------------------
    |
    | URL del servidor que ejecuta las reglas de negocio en Python
    |
    */
    'api_endpoint' => env('WORKFLOW_API_ENDPOINT', 'https://python-server.example.com/api/execute'),
    
    /*
    |--------------------------------------------------------------------------
    | Usar API Mock
    |--------------------------------------------------------------------------
    |
    | Mientras el servidor Python no esté listo, usar respuesta simulada
    |
    */
    'use_mock_api' => env('WORKFLOW_USE_MOCK', true),
    
    /*
    |--------------------------------------------------------------------------
    | Timeout de Ejecución
    |--------------------------------------------------------------------------
    |
    | Tiempo máximo de espera para la respuesta del servidor (segundos)
    |
    */
    'execution_timeout' => env('WORKFLOW_TIMEOUT', 60),
    
    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | Directorio donde se almacenarán los archivos cargados
    |
    */
    'storage_path' => 'workflow-files',
];
```

### Dependencias Necesarias

```bash
composer require maatwebsite/excel  # Para leer archivos Excel
composer require barryvdh/laravel-dompdf  # Para generar PDF
```

### Configuración de Storage

Los archivos cargados se almacenarán en:
```
storage/app/workflow-files/{client_id}/{batch_id}/
```

### Variables de Entorno

Agregar a `.env`:

```env
# Workflow Configuration
WORKFLOW_API_ENDPOINT=https://python-server.example.com/api/execute
WORKFLOW_USE_MOCK=true
WORKFLOW_TIMEOUT=60
```

### Performance

- Los archivos Excel se procesarán en chunks para evitar memory overflow
- La generación de JSON se hará en background job si el batch es muy grande
- Se implementará cache para los workflow types

---

## Cronograma Estimado

1. **Migraciones y Modelos** - 2 horas
2. **Seeders y Roles** - 1 hora
3. **Servicios de Validación y JSON** - 4 horas
4. **Wizard de Carga** - 6 horas
5. **Panel de Ejecución** - 3 horas
6. **Historial y PDF** - 4 horas
7. **Tests** - 3 horas
8. **Refinamiento UI** - 2 horas

**Total estimado:** 25 horas de desarrollo

---

## Próximos Pasos

Una vez aprobado este plan:
1. Crear las migraciones
2. Implementar los modelos
3. Desarrollar los servicios core
4. Construir los componentes Livewire
5. Implementar tests
6. Realizar verificación manual completa
