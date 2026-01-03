# Diagrama de Flujo: Sistema de Conciliación

## Flujo Completo del Sistema

```mermaid
graph TD
    Start([Programador inicia sesión]) --> SelectClient[Selecciona Cliente y Sede]
    SelectClient --> SelectWorkflow[Selecciona Workflow: Conciliación]
    SelectWorkflow --> UploadFiles[Carga 6 archivos Excel]
    
    UploadFiles --> Validate{Validación}
    Validate -->|✓ Nombres correctos| V1[✓]
    Validate -->|✓ Cantidad correcta| V2[✓]
    Validate -->|✓ Estructura válida| V3[✓]
    
    V1 & V2 & V3 --> CreateBatch[Crear WorkflowFileBatch]
    CreateBatch --> SaveFiles[Guardar archivos en storage]
    SaveFiles --> BatchReady[Batch status: validated]
    
    BatchReady --> Execute[Programador ejecuta workflow]
    Execute --> GenerateJSON[Generar JSON: Data: archivo: campos]
    GenerateJSON --> SaveJSON[Guardar en batch.generated_json]
    
    SaveJSON --> CheckAPI{API Mock?}
    CheckAPI -->|Sí| MockResponse[Generar respuesta simulada]
    CheckAPI -->|No| CallAPI[Llamar servidor Python]
    
    MockResponse --> SaveResponse[Guardar en batch.api_response]
    CallAPI --> SaveResponse
    
    SaveResponse --> CreateExecution[Crear WorkflowExecution]
    CreateExecution --> LogAPI[Registrar en api_logs]
    LogAPI --> UpdateStatus[Actualizar status: completed]
    
    UpdateStatus --> ShowTest[Mostrar en /test]
    UpdateStatus --> ShowHistory[Mostrar en Historial]
    
    ShowHistory --> OperatorView[Operador visualiza]
    OperatorView --> DownloadPDF[Descargar PDF]
    
    ShowTest --> ProgrammerReview[Programador revisa JSON]
```

## Arquitectura de Base de Datos

```mermaid
erDiagram
    CLIENTS ||--o{ WORKFLOW_FILE_BATCHES : "tiene"
    WORKFLOW_TYPES ||--o{ WORKFLOW_FILE_BATCHES : "define"
    USERS ||--o{ WORKFLOW_FILE_BATCHES : "carga"
    WORKFLOW_FILE_BATCHES ||--o{ WORKFLOW_UPLOADED_FILES : "contiene"
    WORKFLOW_FILE_BATCHES ||--o{ WORKFLOW_EXECUTIONS : "genera"
    WORKFLOWS ||--o{ WORKFLOW_EXECUTIONS : "ejecuta"
    USERS ||--o{ WORKFLOW_EXECUTIONS : "ejecuta"
    
    CLIENTS {
        bigint id PK
        string company
        string branch_name
        bigint parent_id FK
    }
    
    WORKFLOW_TYPES {
        bigint id PK
        string name "Conciliación"
        string slug
        json required_files
    }
    
    WORKFLOW_FILE_BATCHES {
        bigint id PK
        bigint client_id FK
        bigint branch_id FK
        bigint workflow_type_id FK
        bigint uploaded_by FK
        string status
        json validation_results
        longtext generated_json
        longtext api_response
        timestamp processed_at
    }
    
    WORKFLOW_UPLOADED_FILES {
        bigint id PK
        bigint batch_id FK
        string expected_name
        string file_path
        json columns_found
        boolean structure_valid
    }
    
    WORKFLOW_EXECUTIONS {
        bigint id PK
        bigint workflow_id FK
        bigint file_batch_id FK
        bigint executed_by FK
        string status
        longtext result_json
        longtext logs_json
        timestamp started_at
        timestamp completed_at
    }
```

## Estructura JSON

### JSON Enviado al Servidor

```json
{
  "Data": {
    "Turnos": [
      {
        "Fecha Apertura": "2024-01-15",
        "Hs Ap. Caja": "08:00",
        "Fecha Cierre": "2024-01-15",
        "Hs Cierre Caja": "20:00",
        "TURNO": "1",
        "Encargado": "Juan Pérez",
        "APERTURA CAJA Efectivo": "5000.00",
        "Recuento Efectivo": "45000.00"
      }
    ],
    "Reporte_Ventas": [
      {
        "FechaCierre": "2024-01-15",
        "Comanda": "001",
        "Total": "1500.00",
        "Propina": "150.00",
        "Pagos": "Efectivo",
        "Boleta": "A-0001",
        "Efectivo": "1500.00",
        "Getnet": "0.00",
        "Mercado Pago": "0.00",
        "Cta Cte": "0.00"
      }
    ],
    "Reporte_getnet": [...],
    "Prueba_MP": [...],
    "Devoluciones": [...],
    "Caja_Adicion": [...]
  },
  "metadata": {
    "client_id": 1,
    "branch_id": 2,
    "uploaded_at": "2024-01-15T10:30:00-03:00",
    "workflow_type": "Conciliación"
  }
}
```

### Respuesta del Servidor Python

```json
{
  "status": "success",
  "result": {
    "total_ventas": 125000.50,
    "total_efectivo": 45000.00,
    "total_getnet": 50000.50,
    "total_mp": 30000.00,
    "diferencias": [
      {
        "tipo": "Descuadre en turno 1",
        "monto": -150.00,
        "detalle": "Faltante en cierre de caja"
      },
      {
        "tipo": "Faltante Getnet",
        "monto": -50.50,
        "detalle": "Transacción no registrada"
      }
    ],
    "observaciones": "Conciliación completada con 2 diferencias menores",
    "recomendaciones": [
      "Revisar procedimiento de cierre de turno 1",
      "Verificar sincronización con Getnet"
    ]
  },
  "processed_at": "2024-01-15T10:35:42-03:00"
}
```

## Roles y Permisos

| Acción | Programador | Operador | Admin |
|--------|-------------|----------|-------|
| Cargar archivos | ✅ | ❌ | ✅ |
| Ejecutar workflow | ✅ | ❌ | ✅ |
| Ver historial | ✅ | ✅ | ✅ |
| Descargar PDF | ✅ | ✅ | ✅ |
| Ver /test | ✅ | ❌ | ✅ |

## Checklist de Validación

Durante la carga de archivos, el sistema verifica:

- ✅ **Cantidad de archivos**: Exactamente 6 archivos
- ✅ **Nombres de archivos**: 
  - Turnos.xlsx
  - Reporte_Ventas.xlsx
  - Reporte_getnet.xlsx
  - Prueba_MP.xlsx
  - Devoluciones.xlsx
  - Caja_Adicion.xlsx
- ✅ **Estructura de columnas**: Cada archivo debe tener las columnas especificadas
- ✅ **Formato**: Todos deben ser archivos Excel válidos (.xlsx)
- ✅ **Contenido**: Al menos 1 fila de datos (además del header)
