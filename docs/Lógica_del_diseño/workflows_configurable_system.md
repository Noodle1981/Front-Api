# Sistema de Configuración Editable de Workflows

## Concepto

En lugar de hardcodear las columnas requeridas, crearemos un **sistema configurable** donde podrás:

1. ✅ Ver las columnas actualmente requeridas para cada tipo de archivo
2. ✅ Agregar nuevas columnas requeridas
3. ✅ Remover columnas que ya no son necesarias
4. ✅ Marcar columnas como "opcionales" vs "obligatorias"
5. ✅ Todo desde una interfaz web, sin tocar código

---

## Arquitectura de Base de Datos

### Tabla: `workflow_types`

```php
Schema::create('workflow_types', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // "Conciliación"
    $table->string('slug')->unique(); // "conciliacion"
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### Tabla: `workflow_file_definitions` (NUEVA)

Define qué tipos de archivos se esperan para cada workflow:

```php
Schema::create('workflow_file_definitions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('workflow_type_id')->constrained()->onDelete('cascade');
    $table->string('file_type_name'); // "Turnos", "Reporte_Ventas", etc.
    $table->string('file_type_slug'); // "turnos", "reporte_ventas"
    $table->text('description')->nullable();
    $table->integer('order')->default(0); // Orden de visualización
    $table->boolean('is_required')->default(true);
    $table->timestamps();
    
    $table->unique(['workflow_type_id', 'file_type_slug']);
});
```

### Tabla: `workflow_required_columns` (NUEVA)

Define las columnas requeridas para cada tipo de archivo:

```php
Schema::create('workflow_required_columns', function (Blueprint $table) {
    $table->id();
    $table->foreignId('file_definition_id')->constrained('workflow_file_definitions')->onDelete('cascade');
    $table->string('column_name'); // "Fecha Apertura", "TURNO", etc.
    $table->string('column_name_normalized'); // "fecha apertura" (para matching)
    $table->boolean('is_required')->default(true); // true = obligatoria, false = opcional
    $table->integer('order')->default(0);
    $table->text('description')->nullable(); // "Fecha de apertura de caja"
    $table->string('data_type')->nullable(); // "date", "string", "number"
    $table->timestamps();
});
```

---

## Relaciones de Modelos

```php
// WorkflowType.php
public function fileDefinitions() {
    return $this->hasMany(WorkflowFileDefinition::class);
}

// WorkflowFileDefinition.php
public function workflowType() {
    return $this->belongsTo(WorkflowType::class);
}

public function requiredColumns() {
    return $this->hasMany(WorkflowRequiredColumn::class, 'file_definition_id');
}

// WorkflowRequiredColumn.php
public function fileDefinition() {
    return $this->belongsTo(WorkflowFileDefinition::class);
}
```

---

## Interfaz de Administración

### Componente Livewire: `WorkflowTypeManager`

Ruta: `/admin/workflows/types`

**Vista:**

```
┌─────────────────────────────────────────────────────────────┐
│ 📋 Gestión de Tipos de Workflow                             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ▼ Conciliación                                    [Editar]  │
│   Descripción: Conciliación de datos financieros            │
│   Estado: ✅ Activo                                          │
│   Archivos requeridos: 6                                     │
│                                                              │
│   ┌──────────────────────────────────────────────┐          │
│   │ 1. Turnos (8 columnas requeridas)  [Editar] │          │
│   │ 2. Reporte Ventas (10 columnas)    [Editar] │          │
│   │ 3. Reporte Getnet (5 columnas)     [Editar] │          │
│   │ 4. Ventas MP (3 columnas)          [Editar] │          │
│   │ 5. Devoluciones (7 columnas)       [Editar] │          │
│   │ 6. Caja Adición (5 columnas)       [Editar] │          │
│   └──────────────────────────────────────────────┘          │
│                                                              │
│ [+ Crear Nuevo Workflow Type]                               │
└─────────────────────────────────────────────────────────────┘
```

### Componente Livewire: `FileDefinitionEditor`

Ruta: `/admin/workflows/types/{workflow}/files/{fileDefinition}/edit`

**Vista:**

```
┌─────────────────────────────────────────────────────────────┐
│ ✏️ Editar Definición: Turnos                                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ Nombre: [Turnos                              ]              │
│ Descripción: [Archivo de turnos de caja      ]              │
│                                                              │
│ ── Columnas Requeridas ──────────────────────────────────   │
│                                                              │
│ ✅ Fecha Apertura                            [Obligatoria]  │
│    Tipo: Fecha                               [🗑️ Eliminar]  │
│                                                              │
│ ✅ Hs Ap. Caja                               [Obligatoria]  │
│    Tipo: Texto                               [🗑️ Eliminar]  │
│                                                              │
│ ✅ Fecha Cierre                              [Obligatoria]  │
│    Tipo: Fecha                               [🗑️ Eliminar]  │
│                                                              │
│ ✅ TURNO                                     [Obligatoria]  │
│    Tipo: Número                              [🗑️ Eliminar]  │
│                                                              │
│ ✅ Encargado                                 [Obligatoria]  │
│    Tipo: Texto                               [🗑️ Eliminar]  │
│                                                              │
│ ⚪ Cantidad de comensales                    [Opcional]     │
│    Tipo: Número                              [🗑️ Eliminar]  │
│                                                              │
│ [+ Agregar Nueva Columna]                                   │
│                                                              │
│ ┌────────────────────────────────────────────┐              │
│ │ 💡 Tip: Las columnas marcadas como        │              │
│ │ "Obligatorias" deben estar presentes en   │              │
│ │ el archivo para que sea válido.           │              │
│ └────────────────────────────────────────────┘              │
│                                                              │
│ [Cancelar]                          [Guardar Cambios]       │
└─────────────────────────────────────────────────────────────┘
```

### Modal: Agregar Nueva Columna

```
┌─────────────────────────────────────────┐
│ ➕ Agregar Nueva Columna                │
├─────────────────────────────────────────┤
│                                          │
│ Nombre de la columna:                   │
│ [_____________________________]          │
│                                          │
│ Tipo de dato:                           │
│ ○ Texto                                 │
│ ○ Número                                │
│ ○ Fecha                                 │
│ ○ Booleano                              │
│                                          │
│ ☑️ Columna obligatoria                   │
│                                          │
│ Descripción (opcional):                 │
│ [_____________________________]          │
│ [_____________________________]          │
│                                          │
│ [Cancelar]           [Agregar]          │
└─────────────────────────────────────────┘
```

---

## Flujo de Uso

### Escenario 1: Agregar Nueva Columna Requerida

**Situación:** Descubres que necesitas validar también la columna "Supervisor" en Turnos.

**Pasos:**
1. Ir a `/admin/workflows/types`
2. Expandir "Conciliación"
3. Click en "Editar" en "Turnos"
4. Click en "[+ Agregar Nueva Columna]"
5. Ingresar:
   - Nombre: `Supervisor`
   - Tipo: Texto
   - ✅ Obligatoria
6. Click en "Agregar"
7. Click en "Guardar Cambios"

**Resultado:** A partir de ahora, todos los archivos de Turnos deberán tener la columna "Supervisor".

---

### Escenario 2: Hacer Columna Opcional

**Situación:** "Cantidad de comensales" no siempre está presente, pero no es crítica.

**Pasos:**
1. Editar definición de "Turnos"
2. Encontrar "Cantidad de comensales"
3. Cambiar de "Obligatoria" a "Opcional"
4. Guardar

**Resultado:** El archivo será válido con o sin esta columna.

---

### Escenario 3: Eliminar Columna Obsoleta

**Situación:** Ya no necesitas validar "Comentario Toteat POS" en Caja Adición.

**Pasos:**
1. Editar definición de "Caja Adición"
2. Encontrar "Comentario Toteat POS"
3. Click en "🗑️ Eliminar"
4. Confirmar
5. Guardar

**Resultado:** Esta columna ya no se validará.

---

## Seeder Inicial

```php
// WorkflowTypeSeeder.php
public function run()
{
    $conciliacion = WorkflowType::create([
        'name' => 'Conciliación',
        'slug' => 'conciliacion',
        'description' => 'Conciliación de datos financieros con múltiples fuentes',
        'is_active' => true
    ]);
    
    // Definir archivo "Turnos"
    $turnos = WorkflowFileDefinition::create([
        'workflow_type_id' => $conciliacion->id,
        'file_type_name' => 'Turnos',
        'file_type_slug' => 'turnos',
        'description' => 'Archivo de turnos de caja',
        'order' => 1,
        'is_required' => true
    ]);
    
    // Columnas para Turnos
    $turnosColumns = [
        ['column_name' => 'Fecha Apertura', 'is_required' => true, 'data_type' => 'date'],
        ['column_name' => 'Hs Ap. Caja', 'is_required' => true, 'data_type' => 'string'],
        ['column_name' => 'Fecha Cierre', 'is_required' => true, 'data_type' => 'date'],
        ['column_name' => 'Hs Cierre Caja', 'is_required' => true, 'data_type' => 'string'],
        ['column_name' => 'TURNO', 'is_required' => true, 'data_type' => 'number'],
        ['column_name' => 'Encargado', 'is_required' => true, 'data_type' => 'string'],
        ['column_name' => 'APERTURA CAJA Efectivo', 'is_required' => true, 'data_type' => 'number'],
        ['column_name' => 'Recuento Efectivo', 'is_required' => true, 'data_type' => 'number'],
        ['column_name' => 'Cantidad de comensales', 'is_required' => false, 'data_type' => 'number'], // Opcional
    ];
    
    foreach ($turnosColumns as $index => $col) {
        WorkflowRequiredColumn::create([
            'file_definition_id' => $turnos->id,
            'column_name' => $col['column_name'],
            'column_name_normalized' => $this->normalize($col['column_name']),
            'is_required' => $col['is_required'],
            'data_type' => $col['data_type'],
            'order' => $index
        ]);
    }
    
    // Repetir para los otros 5 tipos de archivo...
}

private function normalize(string $columnName): string
{
    $normalized = strtolower(trim($columnName));
    $normalized = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
        ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
        $normalized
    );
    return preg_replace('/[^a-z0-9\s\/]/', '', $normalized);
}
```

---

## Validación Dinámica

```php
// FileValidationService.php
public function validateBatch(WorkflowType $workflowType, array $uploadedFiles): array
{
    $fileDefinitions = $workflowType->fileDefinitions()
        ->with('requiredColumns')
        ->where('is_required', true)
        ->get();
    
    $results = [
        'valid' => true,
        'matched_files' => [],
        'errors' => []
    ];
    
    $matchedDefinitions = [];
    
    foreach ($uploadedFiles as $file) {
        $columns = $this->extractColumns($file);
        $matchedDef = $this->matchFileDefinition($columns, $fileDefinitions);
        
        if (!$matchedDef) {
            $results['valid'] = false;
            $results['errors'][] = "Archivo '{$file->getClientOriginalName()}' no identificado";
            continue;
        }
        
        // Validar columnas obligatorias
        $missingColumns = $this->checkRequiredColumns($columns, $matchedDef);
        if (!empty($missingColumns)) {
            $results['valid'] = false;
            $results['errors'][] = "Archivo '{$file->getClientOriginalName()}' ({$matchedDef->file_type_name}): Faltan columnas obligatorias: " . implode(', ', $missingColumns);
            continue;
        }
        
        if (in_array($matchedDef->id, $matchedDefinitions)) {
            $results['valid'] = false;
            $results['errors'][] = "Archivo duplicado del tipo '{$matchedDef->file_type_name}'";
            continue;
        }
        
        $matchedDefinitions[] = $matchedDef->id;
        $results['matched_files'][] = [
            'filename' => $file->getClientOriginalName(),
            'type' => $matchedDef->file_type_name,
            'columns' => $columns
        ];
    }
    
    return $results;
}

private function matchFileDefinition(array $uploadedColumns, $fileDefinitions)
{
    foreach ($fileDefinitions as $definition) {
        $requiredCols = $definition->requiredColumns()
            ->where('is_required', true)
            ->get();
        
        $match = true;
        foreach ($requiredCols as $reqCol) {
            if (!$this->columnExists($reqCol->column_name_normalized, $uploadedColumns)) {
                $match = false;
                break;
            }
        }
        
        if ($match) {
            return $definition;
        }
    }
    
    return null;
}

private function checkRequiredColumns(array $uploadedColumns, $fileDefinition): array
{
    $missing = [];
    $requiredCols = $fileDefinition->requiredColumns()
        ->where('is_required', true)
        ->get();
    
    foreach ($requiredCols as $reqCol) {
        if (!$this->columnExists($reqCol->column_name_normalized, $uploadedColumns)) {
            $missing[] = $reqCol->column_name;
        }
    }
    
    return $missing;
}
```

---

## Versionado (Opcional - Futuro)

Si quieres mantener historial de cambios en las configuraciones:

```php
Schema::create('workflow_configuration_versions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('workflow_type_id')->constrained()->onDelete('cascade');
    $table->integer('version_number');
    $table->json('configuration_snapshot'); // Snapshot completo de la config
    $table->foreignId('created_by')->constrained('users');
    $table->text('change_notes')->nullable();
    $table->timestamps();
});
```

---

## Ventajas de Este Sistema

1. ✅ **Sin código**: Cambios desde la UI
2. ✅ **Flexible**: Agrega/quita columnas cuando quieras
3. ✅ **Granular**: Marca columnas como obligatorias u opcionales
4. ✅ **Auditable**: Registro de quién cambió qué y cuándo
5. ✅ **Escalable**: Fácil agregar nuevos tipos de workflow
6. ✅ **Documentado**: Cada columna puede tener descripción
7. ✅ **Tipado**: Puedes especificar tipo de dato esperado

---

## Permisos

Solo usuarios con rol "Programador" o "Admin" pueden:
- Ver `/admin/workflows/types`
- Editar configuraciones de workflow
- Agregar/eliminar columnas

```php
Route::middleware(['role:Programador|Admin'])->group(function () {
    Route::get('/admin/workflows/types', WorkflowTypeManager::class)->name('workflows.types');
    Route::get('/admin/workflows/types/{workflow}/files/{fileDefinition}/edit', FileDefinitionEditor::class)->name('workflows.files.edit');
});
```

---

## Resumen

Con este sistema:
- ✅ Empiezas con las columnas mínimas que conoces ahora
- ✅ Cuando descubras nuevas columnas necesarias, las agregas desde la UI
- ✅ No necesitas tocar código ni hacer deploy
- ✅ Los cambios aplican inmediatamente
- ✅ Puedes hacer columnas opcionales si no siempre están presentes
