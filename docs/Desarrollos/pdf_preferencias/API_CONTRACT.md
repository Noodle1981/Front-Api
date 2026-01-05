# Contrato de API - Servidor Python → Laravel

## Endpoint Esperado

```
POST /api/workflow/execute
```

## Request Body (Lo que Laravel envía)

```json
{
  "workflow_type": "conciliacion",
  "batch_id": 14,
  "files": [
    {
      "type": "caja_adicion",
      "path": "/storage/workflows/batch_14/caja_adicion.xlsx"
    },
    {
      "type": "reporte_ventas",
      "path": "/storage/workflows/batch_14/reporte_ventas.xlsx"
    },
    {
      "type": "reporte_getnet",
      "path": "/storage/workflows/batch_14/reporte_getnet.xlsx"
    },
    {
      "type": "ventas_mp",
      "path": "/storage/workflows/batch_14/ventas_mp.xlsx"
    },
    {
      "type": "turnos",
      "path": "/storage/workflows/batch_14/turnos.xlsx"
    },
    {
      "type": "devoluciones",
      "path": "/storage/workflows/batch_14/devoluciones.xlsx"
    }
  ],
  "client_id": 1,
  "branch_id": 2
}
```

## Response Body (Lo que Python debe devolver)

```json
{
  "success": true,
  "execution_time_ms": 1250,
  "data": {
    "metadata": {
      "fecha": "12/02/2025",
      "dia": "Martes",
      "turno": "MAÑANA",
      "encargado": "Felipe",
      "hs_apertura": "11:10",
      "hs_cierre": "20:19",
      "sucursal": "PARADOR"
    },
    "enviar_sucursal": {
      "total_ventas": "816,500.00",
      "parador": {
        "cantidad_tickets": 9,
        "ticket_promedio": "90,722.22",
        "cantidad_comensales": 32,
        "comensales_promedio": "25,515.63"
      },
      "horarios_venta": {
        "apertura": "11:10",
        "primera_venta": "14:37",
        "ultima_venta": "20:09",
        "cierre": "20:19",
        "intervalo_primera_venta": "3:27",
        "duracion_jornada": "9:09",
        "intervalo_ultima_venta": "0:10"
      },
      "diferencias_caja": {
        "mercado_pago": {
          "real": "169,100.00",
          "real_no_conciliado": "0.00",
          "sistema": "0.00",
          "sistema_no_conciliado": "169,100.00",
          "diferencia": "-169,100.00",
          "porcentaje": "0.00"
        },
        "getnet": {
          "real": "747,740.00",
          "real_no_conciliado": "0.00",
          "sistema": "747,740.00",
          "sistema_no_conciliado": "0.00",
          "diferencia": "0.00",
          "porcentaje": "0.00"
        },
        "efectivo": {
          "apertura_caja": "138,260.00",
          "efectivo_real": "143,900.00",
          "pagos": "91,100.00",
          "recuento_real": "191,060.00",
          "diferencia": "191,060.00",
          "porcentaje": "0.00"
        },
        "cta_cte": {
          "sistema": "0.00",
          "conciliado_sistema": "0.00",
          "real": "0.00"
        }
      },
      "ventas_por_hora": [
        {"hora": "14:00", "monto": 150600},
        {"hora": "15:00", "monto": 566700},
        {"hora": "16:00", "monto": 102440}
      ],
      "facturacion": {
        "real": "841,700.00",
        "ideal": "747,740.00",
        "diferencia": "93,960.00",
        "desvio_porcentaje": "12.57"
      }
    },
    "enviar_egresos": {
      "caja_adicion": [
        {"importe": "5,000.00", "hora": "15:30", "detalle": "Compra de insumos"},
        {"importe": "2,500.00", "hora": "17:45", "detalle": "Pago a proveedor"}
      ],
      "mercado_pago": [
        {"importe": "1,200.00", "hora": "16:20", "detalle": "Devolución cliente"}
      ]
    },
    "enviar_no_conciliados": {
      "mercado_pago": {
        "total_real_no_conciliado": "5,000.00",
        "total_sistema_no_conciliado": "3,500.00",
        "items_real": [
          {"id_venta": "MP-001", "hora": "14:30", "monto": "2,500.00"},
          {"id_venta": "MP-002", "hora": "15:45", "monto": "2,500.00"}
        ],
        "items_sistema": [
          {"id_venta": "MP-003", "hora": "16:00", "monto": "3,500.00"}
        ]
      },
      "getnet": {
        "total_real_no_conciliado": "0.00",
        "total_sistema_no_conciliado": "0.00",
        "items_real": [],
        "items_sistema": []
      },
      "efectivo_cta_cte": {
        "total_real_no_conciliado": "1,500.00",
        "total_sistema_no_conciliado": "1,500.00",
        "items_real": [
          {"id_venta": "EF-001", "hora": "18:00", "monto": "1,500.00"}
        ],
        "items_sistema": [
          {"id_venta": "EF-001", "hora": "18:00", "monto": "1,500.00"}
        ]
      }
    },
    "enviar_anulaciones": [
      {
        "id_comanda": "CMD-123",
        "camarero_mesa": "Juan - Mesa 5",
        "producto": "Hamburguesa Completa",
        "comentario": "Cliente canceló pedido",
        "hora_anulacion": "15:30",
        "precio": "8,500.00"
      },
      {
        "id_comanda": "CMD-124",
        "camarero_mesa": "María - Mesa 8",
        "producto": "Pizza Napolitana",
        "comentario": "Error en pedido",
        "hora_anulacion": "16:45",
        "precio": "12,000.00"
      }
    ]
  }
}
```

## Notas Importantes para el Desarrollador Python

### 1. Formato de Números
- **Usar formato argentino**: punto como separador de miles, coma como decimal
- Ejemplo: `"169,100.00"` (no `169100.00`)

### 2. Formato de Fechas y Horas
- **Fechas**: `"DD/MM/YYYY"` → `"12/02/2025"`
- **Horas**: `"HH:MM"` → `"14:30"`
- **Días**: Nombre completo en español → `"Martes"`

### 3. Arrays Vacíos
- Si no hay datos, devolver array vacío `[]`
- Ejemplo: `"items_real": []`

### 4. Campos Obligatorios
Todos los campos del JSON deben estar presentes, aunque estén vacíos:
- Strings vacíos: `""`
- Arrays vacíos: `[]`
- Números en cero: `"0.00"`

### 5. Cálculos Esperados

#### Diferencias de Caja
```
diferencia = real - sistema
porcentaje = (diferencia / real) * 100
```

#### Facturación
```
diferencia = real - ideal
desvio_porcentaje = (diferencia / ideal) * 100
```

### 6. Manejo de Errores

Si hay un error en el procesamiento:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "El archivo de ventas MP está corrupto",
    "details": "Error en línea 45: formato de fecha inválido"
  }
}
```

## Ejemplo de Uso

### Paso 1: Laravel envía archivos
```bash
curl -X POST http://python-server:5000/api/workflow/execute \
  -H "Content-Type: application/json" \
  -d @request.json
```

### Paso 2: Python procesa archivos Excel
- Lee cada archivo Excel
- Realiza cálculos de conciliación
- Genera estadísticas

### Paso 3: Python devuelve JSON
- Laravel recibe el JSON
- Lo almacena en `workflow_executions.response_data`
- Lo usa para generar el PDF

## Testing

Puedes probar tu endpoint con este comando:

```bash
curl -X POST http://localhost:5000/api/workflow/execute \
  -H "Content-Type: application/json" \
  -d '{
    "workflow_type": "conciliacion",
    "batch_id": 1,
    "files": [
      {"type": "caja_adicion", "path": "/path/to/file.xlsx"}
    ]
  }'
```

Deberías recibir el JSON completo con todos los datos calculados.
