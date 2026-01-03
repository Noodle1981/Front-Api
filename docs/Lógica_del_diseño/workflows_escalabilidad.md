# Escalabilidad del Sistema de Workflows

## Visión General

El sistema está diseñado para ser **completamente escalable** y soportar múltiples tipos de workflows más allá de "Conciliación".

---

## Escalabilidad por Dimensiones

### 1. Múltiples Workflows

**Capacidad:** Ilimitada

Puedes crear tantos workflows como necesites:

```
✅ Conciliación (actual)
✅ Inventario
✅ Nómina
✅ Facturación
✅ Auditoría Fiscal
✅ Control de Calidad
✅ ... cualquier otro
```

**Cómo agregar un nuevo workflow:**

1. Ir a `/admin/workflows/types`
2. Click en "Crear Nuevo Workflow Type"
3. Definir:
   - Nombre: "Inventario"
   - Descripción: "Control de inventario mensual"
   - Archivos requeridos: 3
4. Para cada archivo requerido:
   - Definir nombre del tipo de archivo
   - Definir columnas requeridas
5. Guardar

**Ejemplo - Workflow "Inventario":**

```json
{
  "name": "Inventario",
  "files": [
    {
      "type": "Stock_Actual",
      "columns": ["Código", "Producto", "Cantidad", "Ubicación"]
    },
    {
      "type": "Movimientos",
      "columns": ["Fecha", "Tipo", "Código", "Cantidad", "Usuario"]
    },
    {
      "type": "Ajustes",
      "columns": ["Código", "Cantidad_Anterior", "Cantidad_Nueva", "Motivo"]
    }
  ]
}
```

---

### 2. Archivos por Workflow

**Capacidad:** Configurable (no limitado a 6)

Cada workflow puede tener diferente cantidad de archivos:

- Conciliación: 6 archivos
- Inventario: 3 archivos
- Nómina: 8 archivos
- Facturación: 2 archivos

**Validación dinámica:** El sistema valida según la configuración de cada workflow.

---

### 3. Columnas por Archivo

**Capacidad:** Ilimitada y editable

Cada tipo de archivo puede tener:
- Columnas obligatorias
- Columnas opcionales
- Sin límite de cantidad

**Flexibilidad:**
- Agregar columnas nuevas cuando se descubran
- Marcar columnas como opcionales si no siempre están presentes
- Eliminar columnas obsoletas

---

### 4. Reglas de Negocio en Python

**Libertad Total para el Programador**

El JSON enviado al servidor Python contiene **TODOS** los datos de **TODAS** las columnas:

```json
{
  "Data": {
    "Turnos": [
      {
        "Fecha": "2024-01-15",
        "Fecha Apertura": "2024-01-15",
        "Hs Ap. Caja": "08:00",
        "Fecha Cierre": "2024-01-15",
        "Hs Cierre Caja": "20:00",
        "TURNO": "1",
        "Encargado": "Juan Pérez",
        "APERTURA CAJA Efectivo": "5000.00",
        "Recuento Efectivo": "45000.00",
        "Cantidad de comensales": "150"
        // ... TODAS las columnas del Excel
      }
    ],
    "Reporte_Ventas": [...],
    // ... todos los archivos
  },
  "metadata": {
    "workflow_type": "Conciliación",
    "client_id": 1,
    "branch_id": 2
  }
}
```

**El programador puede:**
- ✅ Usar cualquier columna en sus reglas de negocio
- ✅ Combinar datos de múltiples archivos
- ✅ Crear cálculos complejos
- ✅ Aplicar lógica condicional
- ✅ Generar reportes personalizados

**Ejemplo de regla en Python:**

```python
def validar_conciliacion(data):
    turnos = data['Data']['Turnos']
    ventas = data['Data']['Reporte_Ventas']
    
    # Usar cualquier campo disponible
    total_recuento = sum(float(t['Recuento Efectivo']) for t in turnos)
    total_efectivo_ventas = sum(float(v['Efectivo']) for v in ventas)
    
    diferencia = total_recuento - total_efectivo_ventas
    
    # Usar campos opcionales si existen
    comensales_total = sum(int(t.get('Cantidad de comensales', 0)) for t in turnos)
    
    return {
        'total_recuento': total_recuento,
        'total_ventas': total_efectivo_ventas,
        'diferencia': diferencia,
        'comensales': comensales_total,
        'status': 'ok' if abs(diferencia) < 100 else 'warning'
    }
```

---

### 5. Múltiples Clientes y Sedes

**Capacidad:** Ilimitada

El mismo workflow puede ejecutarse para:
- Diferentes clientes
- Diferentes sedes del mismo cliente
- Con los mismos archivos pero datos diferentes

**Ejemplo:**

```
Cliente A - Sede Centro → Workflow Conciliación → Archivos de Enero
Cliente A - Sede Norte → Workflow Conciliación → Archivos de Enero
Cliente B - Sede Única → Workflow Conciliación → Archivos de Enero
```

Cada ejecución es independiente y se almacena por separado.

---

## Arquitectura Escalable

### Base de Datos

```
workflow_types (1)
    ↓
workflow_file_definitions (N) ← Cada workflow puede tener N archivos
    ↓
workflow_required_columns (M) ← Cada archivo puede tener M columnas
```

**Ventajas:**
- ✅ Normalizada
- ✅ Sin duplicación de datos
- ✅ Fácil de consultar
- ✅ Fácil de modificar

---

### JSON Flexible

El JSON generado incluye **TODO**, no solo lo validado:

```json
{
  "Data": {
    "archivo1": [
      {
        "columna_validada_1": "...",
        "columna_validada_2": "...",
        "columna_extra_1": "...",      // ← También se incluye
        "columna_extra_2": "...",      // ← También se incluye
        "columna_nueva": "..."         // ← También se incluye
      }
    ]
  }
}
```

**Beneficio:** El programador Python tiene acceso a **todos** los datos, no solo a los validados.

---

## Casos de Uso Futuros

### Caso 1: Agregar Workflow "Nómina"

**Archivos necesarios:**
1. Empleados.xlsx
2. Asistencias.xlsx
3. Horas_Extra.xlsx
4. Deducciones.xlsx
5. Bonos.xlsx

**Proceso:**
1. Crear workflow "Nómina"
2. Definir 5 tipos de archivo
3. Definir columnas para cada uno
4. Crear regla de negocio en Python
5. Listo para usar

**Tiempo estimado:** 30 minutos de configuración

---

### Caso 2: Modificar Workflow Existente

**Situación:** Descubres que "Turnos" también tiene columna "Supervisor"

**Proceso:**
1. Ir a configuración de "Conciliación"
2. Editar "Turnos"
3. Agregar columna "Supervisor" (opcional)
4. Guardar

**Tiempo:** 2 minutos

**Impacto:**
- ✅ Próximas cargas validarán la nueva columna
- ✅ El JSON incluirá "Supervisor" si existe
- ✅ Python puede usar "Supervisor" en reglas
- ✅ Cargas anteriores no se ven afectadas

---

### Caso 3: Workflow con Archivos Opcionales

**Situación:** Algunos meses hay "Devoluciones", otros no.

**Solución:**
En `workflow_file_definitions`, marcar "Devoluciones" como `is_required = false`

**Resultado:**
- ✅ Si se carga, se valida
- ✅ Si no se carga, no hay error
- ✅ Python recibe el archivo solo si existe

---

## Vista de Prueba de Reglas (Futuro)

**Concepto:** Interfaz para que el Programador pruebe reglas de negocio sin ejecutar workflow completo.

**Características propuestas:**

1. **Editor de Reglas Python**
   - Escribir código Python en el navegador
   - Syntax highlighting
   - Autocompletado de campos disponibles

2. **Datos de Prueba**
   - Usar batch real anterior
   - O cargar archivos de prueba
   - O generar datos sintéticos

3. **Ejecución en Sandbox**
   - Ejecutar regla sin afectar producción
   - Ver resultado en tiempo real
   - Ver logs de ejecución

4. **Debugging**
   - Ver valores intermedios
   - Detectar errores
   - Optimizar performance

**Mockup:**

```
┌─────────────────────────────────────────────────────────────┐
│ 🧪 Prueba de Reglas de Negocio                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ Workflow: [Conciliación ▼]                                  │
│ Batch de prueba: [Batch #123 - 15/01/2024 ▼]               │
│                                                              │
│ ┌────────────────────────────────────────────────────────┐  │
│ │ def validar_conciliacion(data):                        │  │
│ │     turnos = data['Data']['Turnos']                    │  │
│ │     ventas = data['Data']['Reporte_Ventas']            │  │
│ │                                                         │  │
│ │     total_recuento = sum(...)                          │  │
│ │     total_ventas = sum(...)                            │  │
│ │                                                         │  │
│ │     return {                                            │  │
│ │         'diferencia': total_recuento - total_ventas    │  │
│ │     }                                                   │  │
│ └────────────────────────────────────────────────────────┘  │
│                                                              │
│ [Ejecutar Prueba]                                            │
│                                                              │
│ ── Resultado ──────────────────────────────────────────────  │
│ ✅ Ejecución exitosa (1.2s)                                  │
│                                                              │
│ {                                                            │
│   "diferencia": -150.50,                                     │
│   "status": "warning"                                        │
│ }                                                            │
└─────────────────────────────────────────────────────────────┘
```

---

## Resumen de Escalabilidad

| Aspecto | Escalabilidad | Configuración |
|---------|---------------|---------------|
| **Workflows** | Ilimitados | UI Admin |
| **Archivos por workflow** | Configurable | UI Admin |
| **Columnas por archivo** | Ilimitadas | UI Admin |
| **Clientes** | Ilimitados | Sistema |
| **Sedes** | Ilimitadas | Sistema |
| **Ejecuciones** | Ilimitadas | Sistema |
| **Reglas Python** | Personalizables | Código Python |
| **Datos en JSON** | Todos los campos | Automático |

---

## Conclusión

✅ **Sí, el sistema es totalmente escalable**

**Libertad del Programador:**
- Puede crear cualquier workflow
- Puede definir cualquier estructura de archivos
- Tiene acceso a todos los datos
- Puede escribir reglas de negocio complejas en Python
- Puede probar reglas antes de producción (futuro)

**Sin limitaciones técnicas:**
- No hay límites hardcodeados
- Todo es configurable
- Fácil de extender
- Fácil de mantener
