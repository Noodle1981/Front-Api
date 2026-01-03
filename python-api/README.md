# Workflow Python API

API en Python (FastAPI) para procesar workflows de Excel.

## Instalación

```bash
cd python-api
pip install -r requirements.txt
```

## Ejecutar

```bash
# Desarrollo
uvicorn main:app --reload --port 8000

# Producción
uvicorn main:app --host 0.0.0.0 --port 8000
```

## Endpoints

### POST /api/execute
Ejecuta un workflow de conciliación.

**Request:**
```json
{
  "metadata": {
    "batch_code": "WF-20260103-ABC123",
    "workflow_type": "Conciliación",
    "client_id": 1,
    "branch_id": 5,
    "uploaded_at": "2026-01-03T10:30:00-03:00",
    "total_files": 6
  },
  "Data": {
    "archivo_1": [...],
    "archivo_2": [...],
    ...
  }
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Workflow ejecutado correctamente",
  "results": {
    "total_records": 150,
    "valid_records": 145,
    "invalid_records": 5,
    "errors": [],
    "warnings": []
  },
  "execution_time_ms": 1250
}
```

### GET /health
Health check del servicio.

## Configuración en Laravel

En `.env`:
```
WORKFLOW_PYTHON_API_URL=http://localhost:8000/api/execute
WORKFLOW_USE_MOCK_API=false
```

## Desarrollo

El archivo `main.py` contiene un ejemplo básico. Debes implementar tu lógica de negocio en la función `execute_workflow()`.

### Estructura recomendada:

```
python-api/
├── main.py              # API principal
├── requirements.txt     # Dependencias
├── services/
│   ├── validator.py     # Validaciones
│   ├── processor.py     # Procesamiento
│   └── calculator.py    # Cálculos
└── tests/
    └── test_api.py      # Tests
```
