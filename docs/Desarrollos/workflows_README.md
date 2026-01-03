# Sistema de Workflows - Índice de Documentación

## 📋 Resumen

Sistema escalable para carga, validación y procesamiento de archivos Excel con ejecución de reglas de negocio en Python.

**Workflow Principal:** Conciliación de datos financieros  
**Archivos:** 6 tipos de Excel  
**Roles:** Programador (carga/ejecuta), Operador (consulta)  
**Estado:** En planificación

---

## 📚 Documentación Completa

### 1. Planificación y Diseño

#### [Plan de Implementación](workflows_implementation_plan.md)
**Ubicación:** `docs/Desarrollos/workflows_implementation_plan.md`

Documento técnico completo con:
- Arquitectura de base de datos (4 nuevas tablas)
- Modelos Eloquent y relaciones
- Servicios (validación, JSON, ejecución, PDF)
- Componentes Livewire (wizard, panel, historial)
- Rutas y controladores
- Plan de verificación
- Cronograma estimado (25 horas)

**Cuándo leer:** Antes de comenzar implementación

---

#### [Diagrama de Flujo y Arquitectura](../Lógica_del_diseño/workflows_diagrama.md)
**Ubicación:** `docs/Lógica_del_diseño/workflows_diagrama.md`

Diagramas visuales:
- Flujo completo del sistema (Mermaid)
- Arquitectura de base de datos (ERD)
- Estructura JSON enviada/recibida
- Tabla de roles y permisos
- Checklist de validación

**Cuándo leer:** Para entender el flujo general

---

### 2. Análisis de Datos

#### [Análisis de Archivos Excel](workflows_excel_analysis.md)
**Ubicación:** `docs/Desarrollos/workflows_excel_analysis.md`

Análisis detallado de los 6 archivos Excel reales:
- Estructura de columnas por archivo
- Cantidad de columnas y filas
- Discrepancias con requerimientos originales
- Nombres de archivos reales vs esperados
- Recomendaciones para validación

**Cuándo leer:** Para conocer la estructura exacta de los Excel

---

### 3. Lógica de Validación

#### [Estrategia de Validación](../Lógica_del_diseño/workflows_validation_strategy.md)
**Ubicación:** `docs/Lógica_del_diseño/workflows_validation_strategy.md`

Sistema inteligente de validación:
- Matching por estructura de columnas (no por nombre de archivo)
- Algoritmo de identificación automática
- Normalización de nombres de columnas
- Casos de uso y ejemplos
- Mensajes de error
- Código de implementación

**Cuándo leer:** Para entender cómo funciona la validación

---

### 4. Sistema Configurable

#### [Sistema de Configuración Editable](../Lógica_del_diseño/workflows_configurable_system.md)
**Ubicación:** `docs/Lógica_del_diseño/workflows_configurable_system.md`

Sistema UI para gestionar columnas requeridas:
- Arquitectura de tablas configurables
- Interfaz de administración (mockups)
- Editor de columnas
- Validación dinámica
- Flujos de uso
- Versionado (futuro)

**Cuándo leer:** Para entender cómo modificar columnas sin código

---

### 5. Escalabilidad

#### [Escalabilidad del Sistema](../Lógica_del_diseño/workflows_escalabilidad.md)
**Ubicación:** `docs/Lógica_del_diseño/workflows_escalabilidad.md`

Capacidades de escalamiento:
- Múltiples workflows (ilimitados)
- Archivos configurables por workflow
- Columnas ilimitadas y editables
- Libertad para reglas Python
- Casos de uso futuros
- Vista de prueba de reglas (propuesta)

**Cuándo leer:** Para entender el potencial del sistema

---

#### [Análisis de Viabilidad y ROI](workflows_roi_analysis.md) ⭐
**Ubicación:** `docs/Desarrollos/workflows_roi_analysis.md`

Análisis completo de viabilidad del proyecto:
- Factores de éxito y riesgos mitigados
- Dashboard de ROI propuesto (mockups)
- Métricas clave (tiempo ahorrado, ahorro monetario, ROI)
- Arquitectura de BD para métricas
- Implementación técnica completa
- Caso de uso real con números
- Propuesta de Sprint Extra para dashboard

**Cuándo leer:** Para justificar el proyecto ante stakeholders/dueños

---

## 🗂️ Estructura de Archivos

```
docs/
├── Desarrollos/
│   ├── nuevos_requerimientos.md          # Requerimientos originales
│   ├── workflows_implementation_plan.md   # ⭐ Plan de implementación
│   ├── workflows_excel_analysis.md        # Análisis de Excel reales
│   ├── workflows_roi_analysis.md          # ⭐ Análisis de viabilidad y ROI
│   └── workflows_README.md                # Este archivo
│
└── Lógica_del_diseño/
    ├── workflows_diagrama.md              # Diagramas y flujos
    ├── workflows_validation_strategy.md   # Estrategia de validación
    ├── workflows_configurable_system.md   # Sistema configurable
    ├── workflows_escalabilidad.md         # Escalabilidad
    └── excels/                            # Archivos Excel de ejemplo
        ├── Turnos.xlsx
        ├── Reporte Ventas.xlsx
        ├── Reporte getnet.xlsx
        ├── Ventas MP.xlsx
        ├── Devoluciones.xlsx
        └── Caja Adicion.xlsx
```

---

## 🎯 Orden de Lectura Recomendado

### Para Desarrolladores (Primera Vez)

1. **[Diagrama de Flujo](../Lógica_del_diseño/workflows_diagrama.md)** - Entender el panorama general
2. **[Análisis de Excel](workflows_excel_analysis.md)** - Conocer los datos reales
3. **[Estrategia de Validación](../Lógica_del_diseño/workflows_validation_strategy.md)** - Entender la lógica
4. **[Plan de Implementación](workflows_implementation_plan.md)** - Detalles técnicos
5. **[Sistema Configurable](../Lógica_del_diseño/workflows_configurable_system.md)** - UI de administración

### Para Product Owners / Stakeholders

1. **[Escalabilidad](../Lógica_del_diseño/workflows_escalabilidad.md)** - Capacidades del sistema
2. **[Diagrama de Flujo](../Lógica_del_diseño/workflows_diagrama.md)** - Flujo de usuario
3. **[Sistema Configurable](../Lógica_del_diseño/workflows_configurable_system.md)** - Gestión sin código

### Para Mantenimiento Futuro

1. **[Sistema Configurable](../Lógica_del_diseño/workflows_configurable_system.md)** - Cómo agregar columnas
2. **[Escalabilidad](../Lógica_del_diseño/workflows_escalabilidad.md)** - Cómo agregar workflows

---

## 🔑 Conceptos Clave

### Workflow Type
Tipo de proceso (ej: "Conciliación", "Inventario", "Nómina")

### File Definition
Tipo de archivo esperado dentro de un workflow (ej: "Turnos", "Reporte Ventas")

### Required Column
Columna que debe estar presente en un archivo para ser válido

### Batch
Conjunto de archivos cargados en una sola operación

### Execution
Ejecución de un workflow sobre un batch (envío a Python + resultado)

---

## 📊 Tablas de Base de Datos

### Principales

- `workflow_types` - Tipos de workflow
- `workflow_file_definitions` - Definiciones de archivos por workflow
- `workflow_required_columns` - Columnas requeridas por archivo
- `workflow_file_batches` - Batches de archivos cargados
- `workflow_uploaded_files` - Archivos individuales de un batch
- `workflow_executions` - Ejecuciones de workflows

### Existentes (Reutilizadas)

- `clients` - Clientes
- `users` - Usuarios
- `api_logs` - Logs de API
- `roles` - Roles (Spatie Permissions)

---

## 🚀 Próximos Pasos

### Fase 1: Base de Datos
- [ ] Crear migraciones
- [ ] Crear modelos
- [ ] Crear seeders
- [ ] Ejecutar migraciones

### Fase 2: Servicios
- [ ] FileValidationService
- [ ] WorkflowJsonGeneratorService
- [ ] WorkflowExecutionService
- [ ] WorkflowPdfService

### Fase 3: UI
- [ ] WorkflowFileUploadWizard (Livewire)
- [ ] WorkflowExecutionPanel (Livewire)
- [ ] WorkflowHistoryTable (Livewire)
- [ ] WorkflowTypeManager (Livewire)
- [ ] FileDefinitionEditor (Livewire)

### Fase 4: Integración
- [ ] Rutas
- [ ] Controladores
- [ ] Vista /test
- [ ] Configuración

### Fase 5: Testing
- [ ] Tests unitarios
- [ ] Tests de integración
- [ ] Verificación manual

---

## 📞 Contacto

Para preguntas sobre esta documentación o el sistema de workflows, contactar al equipo de desarrollo.

---

**Última actualización:** 2026-01-03  
**Versión:** 1.0.0  
**Estado:** Planificación completa
