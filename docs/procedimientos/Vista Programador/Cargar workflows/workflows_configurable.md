# Sistema Configurable de Workflows

> **Estado:** 📋 PROPUESTA DE IMPLEMENTACIÓN FUTURA  
> **Prioridad:** Media  
> **Última actualización:** 2026-01-08

---

## Resumen Ejecutivo

Este documento propone un sistema para configurar workflows **sin tocar código**, permitiendo a administradores definir tipos de workflows, archivos requeridos y columnas desde una interfaz web.

### Estado Actual vs Propuesto

| Aspecto | Estado Actual | Propuesta |
|---------|---------------|-----------|
| Tipos de workflow | Hardcoded (solo Conciliación) | Configurable desde UI |
| Archivos requeridos | Definidos en código | Definidos en BD |
| Columnas | En tablas de BD | CRUD desde admin panel |
| Agregar workflow | Requiere desarrollo | Sin código, desde UI |

---

## Análisis: ¿Generador de Workflows vs Reglas de Negocio?

### Diferencias Clave

| Aspecto | Generador de Workflows | Reglas de Negocio |
|---------|----------------------|-------------------|
| **Propósito** | Definir QUÉ archivos y columnas se necesitan | Definir CÓMO procesar los datos |
| **Responsable** | Laravel (validación estructural) | Servidor Python (lógica de negocio) |
| **Salida** | Validación de archivos | JSON con resultados calculados |
| **Complejidad** | Configuración simple | Lógica compleja de negocio |
| **Usuarios** | Programadores/Admins | Desarrolladores Python |

### Recomendación: **SON SISTEMAS SEPARADOS**

✅ **Generador de Workflows** (Laravel)
- Define estructura de archivos
- Valida columnas presentes
- Configurable desde UI
- **NO ejecuta lógica de negocio**

✅ **Reglas de Negocio** (Servidor Python)
- Recibe datos validados
- Aplica cálculos y lógica
- Genera resultados
- **NO se preocupa por validación estructural**

---

## Arquitectura de Base de Datos

### Tablas Existentes (Ya Implementadas)

```
✅ workflow_types           - Tipos de workflow
✅ workflow_file_definitions - Definición de archivos por workflow
✅ workflow_required_columns - Columnas requeridas por archivo
✅ workflow_file_batches     - Batches de archivos cargados
✅ workflow_uploaded_files   - Archivos individuales
✅ workflow_executions       - Ejecuciones de workflows
```

### Lo que Falta: Interfaz de Administración

Actualmente las tablas existen pero se gestionan solo por seeders/código. La propuesta es crear una UI para administrarlas.

---

## Interfaz de Administración (Propuesta)

### Vista Principal: Gestión de Workflows

**Ruta propuesta:** `/admin/workflows/generator`

```
┌─────────────────────────────────────────────────────────────┐
│ 🔧 Generador de Workflows                                   │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ [+ Crear Nuevo Workflow]                                    │
│                                                              │
│ ▼ Conciliación                          ✅ Activo [Editar] │
│   Descripción: Conciliación de datos financieros            │
│   Archivos requeridos: 6                                     │
│                                                              │
│   ┌──────────────────────────────────────────────┐          │
│   │ 📄 Turnos (8 columnas)          [Editar]    │          │
│   │ 📄 Reporte Ventas (10 columnas) [Editar]    │          │
│   │ 📄 Reporte Getnet (5 columnas)  [Editar]    │          │
│   │ 📄 Ventas MP (3 columnas)       [Editar]    │          │
│   │ 📄 Devoluciones (7 columnas)    [Editar]    │          │
│   │ 📄 Caja Adición (5 columnas)    [Editar]    │          │
│   │                                               │          │
│   │ [+ Agregar Archivo]                          │          │
│   └──────────────────────────────────────────────┘          │
│                                                              │
│ ▶ Inventario                            ⚪ Inactivo         │
│   (Click para expandir)                                      │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

### Editor de Columnas

**Ruta propuesta:** `/admin/workflows/generator/{workflow}/files/{file}/edit`

```
┌─────────────────────────────────────────────────────────────┐
│ ✏️ Editar Definición: Turnos (Conciliación)                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ── Columnas Requeridas ──────────────────────────────────   │
│                                                              │
│ 1. ✅ Fecha Apertura       [Obligatoria ☑️]    [🗑️]        │
│ 2. ✅ Hs Ap. Caja          [Obligatoria ☑️]    [🗑️]        │
│ 3. ✅ TURNO                [Obligatoria ☑️]    [🗑️]        │
│ 4. ⚪ Supervisor           [Opcional ☐]        [🗑️]        │
│                                                              │
│ [+ Agregar Columna]                                         │
│                                                              │
│ [Cancelar]                          [Guardar Cambios]       │
└─────────────────────────────────────────────────────────────┘
```

---

## Casos de Uso

### Caso 1: Crear Workflow "Inventario"

**Tiempo estimado:** 15-20 minutos

1. Ir a `/admin/workflows/generator`
2. Click "Crear Nuevo Workflow"
3. Completar: Nombre, Slug, Descripción
4. Agregar archivos con sus columnas
5. Guardar

**Resultado:** El workflow aparece en el wizard de carga sin código.

### Caso 2: Agregar Columna a Workflow Existente

**Tiempo estimado:** 2 minutos

1. Editar archivo existente
2. Click "+ Agregar Columna"
3. Definir nombre y si es obligatoria
4. Guardar

**Resultado:** Próximas cargas validan la nueva columna.

---

## Roadmap de Implementación

### ✅ Fase 0: Fundación (COMPLETADO)
- [x] Migraciones de tablas workflow_*
- [x] Modelos con relaciones
- [x] Seeder de workflow "Conciliación"
- [x] Wizard de carga funcionando
- [x] Validación por columnas

### 📋 Fase 1: UI de Administración (PENDIENTE)
- [ ] Componente Livewire `WorkflowGenerator`
- [ ] CRUD de tipos de workflow
- [ ] CRUD de archivos por workflow
- [ ] CRUD de columnas por archivo

### 📋 Fase 2: Validación Dinámica (PENDIENTE)
- [ ] Migrar hardcoded → consulta BD
- [ ] Actualizar `FileValidationService`
- [ ] Tests de integración

### 📋 Fase 3: Mejoras (FUTURO)
- [ ] Versionado de configuraciones
- [ ] Importar/exportar workflows
- [ ] Templates predefinidos
- [ ] Validaciones avanzadas (regex, rangos)

---

## Ventajas del Sistema

### Para el Negocio
- ✅ **Escalable** - Agregar workflows sin desarrollo
- ✅ **Adaptable** - Responde rápido a cambios del cliente
- ✅ **Auditable** - Registro de modificaciones

### Para el Equipo Técnico
- ✅ **Sin deploys** - Cambios inmediatos desde UI
- ✅ **Separación clara** - Laravel valida, Python procesa
- ✅ **Modular** - Cada workflow es independiente

---

## Conclusión

### ✅ Recomendación

Este sistema es **100% implementable** porque la fundación ya existe (tablas, modelos, relaciones). Solo falta construir la interfaz de administración.

**Prioridad sugerida:** Después de estabilizar el MVP actual, implementar cuando se necesite agregar un segundo tipo de workflow.

