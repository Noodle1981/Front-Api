"""
Workflow API - Python Backend
FastAPI server para procesar workflows de Excel

Instalación:
pip install fastapi uvicorn pandas openpyxl

Ejecutar:
uvicorn main:app --reload --port 8000
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Dict, List, Any, Optional
import time

app = FastAPI(title="Workflow API", version="1.0.0")

# CORS para permitir requests desde Laravel
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # En producción, especificar dominio
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# Modelos de datos
class WorkflowMetadata(BaseModel):
    batch_code: str
    workflow_type: str
    client_id: int
    branch_id: int
    uploaded_at: str
    uploaded_by: Optional[str] = None
    total_files: int


class WorkflowRequest(BaseModel):
    metadata: WorkflowMetadata
    Data: Dict[str, List[Dict[str, Any]]]


class WorkflowResponse(BaseModel):
    status: str
    message: str
    results: Dict[str, Any]
    execution_time_ms: int


@app.get("/")
def root():
    return {
        "service": "Workflow API",
        "version": "1.0.0",
        "status": "running"
    }


@app.post("/api/execute", response_model=WorkflowResponse)
async def execute_workflow(request: WorkflowRequest):
    """
    Ejecutar workflow de conciliación
    
    Recibe JSON con metadata y datos de 6 archivos Excel
    Retorna resultados del procesamiento
    """
    start_time = time.time()
    
    try:
        # Extraer datos
        metadata = request.metadata
        data = request.Data
        
        # Validar que tengamos los 6 archivos esperados
        expected_files = 6
        if len(data) != expected_files:
            raise HTTPException(
                status_code=400,
                detail=f"Se esperaban {expected_files} archivos, recibidos: {len(data)}"
            )
        
        # AQUÍ VA TU LÓGICA DE NEGOCIO
        # Ejemplo: procesar cada archivo
        total_records = 0
        valid_records = 0
        invalid_records = 0
        errors = []
        warnings = []
        
        for file_key, file_data in data.items():
            total_records += len(file_data)
            
            # Ejemplo de validación simple
            for row in file_data:
                # Aquí validarías cada fila según tus reglas de negocio
                # Por ahora, simulamos que el 90% son válidos
                if len(row) > 0:  # Validación dummy
                    valid_records += 1
                else:
                    invalid_records += 1
                    errors.append(f"Fila vacía en {file_key}")
        
        # Calcular tiempo de ejecución
        execution_time = int((time.time() - start_time) * 1000)
        
        # Retornar respuesta
        return WorkflowResponse(
            status="success",
            message=f"Workflow '{metadata.workflow_type}' ejecutado correctamente",
            results={
                "total_records": total_records,
                "valid_records": valid_records,
                "invalid_records": invalid_records,
                "errors": errors[:10],  # Máximo 10 errores
                "warnings": warnings,
                "batch_code": metadata.batch_code,
                "processed_files": len(data)
            },
            execution_time_ms=execution_time
        )
        
    except Exception as e:
        execution_time = int((time.time() - start_time) * 1000)
        
        return WorkflowResponse(
            status="failed",
            message=f"Error al ejecutar workflow: {str(e)}",
            results={
                "total_records": 0,
                "valid_records": 0,
                "invalid_records": 0,
                "errors": [str(e)],
                "warnings": []
            },
            execution_time_ms=execution_time
        )


@app.get("/health")
def health_check():
    """Health check endpoint"""
    return {"status": "healthy", "timestamp": time.time()}


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
