# Roadmap: Sistema de Workflows

## 🎯 Objetivo General

Implementar un sistema escalable de carga, validación y procesamiento de archivos Excel con ejecución de reglas de negocio en Python.

**Duración Total Estimada:** 6 sprints (12 semanas)  
**Inicio Propuesto:** Enero 2026  
**Fin Estimado:** Marzo 2026

---

## 📊 Visión General de Sprints

| Sprint | Duración | Objetivo | Entregables |
|--------|----------|----------|-------------|
| **Sprint 1** | 2 semanas | Fundación de datos | Migraciones, modelos, seeders |
| **Sprint 2** | 2 semanas | Servicios core | Validación, JSON, ejecución |
| **Sprint 3** | 2 semanas | Wizard de carga | UI de carga y validación |
| **Sprint 4** | 2 semanas | Ejecución e historial | Panel de ejecución, historial |
| **Sprint 5** | 2 semanas | Configuración UI | Admin de workflows |
| **Sprint 6** | 2 semanas | Testing y refinamiento | Tests, PDF, optimización |

---

## 🚀 Sprint 1: Fundación de Datos
**Duración:** 2 semanas  
**Objetivo:** Establecer la base de datos y modelos

### Historias de Usuario

#### US-1.1: Como desarrollador, necesito las tablas de base de datos
**Puntos:** 5  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Migración `create_workflow_types_table` creada
- ✅ Migración `create_workflow_file_definitions_table` creada
- ✅ Migración `create_workflow_required_columns_table` creada
- ✅ Migración `create_workflow_file_batches_table` creada
- ✅ Migración `create_workflow_uploaded_files_table` creada
- ✅ Migración `modify_workflow_executions_table` creada
- ✅ Todas las migraciones ejecutadas sin errores

**Tareas:**
- [x] Crear migración `workflow_types`
- [x] Crear migración `workflow_file_definitions`
- [x] Crear migración `workflow_required_columns`
- [x] Crear migración `workflow_file_batches`
- [x] Crear migración `workflow_uploaded_files`
- [x] Modificar migración `workflow_executions`
- [x] Ejecutar `php artisan migrate`
- [x] Verificar estructura en BD

---

#### US-1.2: Como desarrollador, necesito los modelos Eloquent
**Puntos:** 3  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Modelo `WorkflowType` con relaciones
- ✅ Modelo `WorkflowFileDefinition` con relaciones
- ✅ Modelo `WorkflowRequiredColumn` con relaciones
- ✅ Modelo `WorkflowFileBatch` con relaciones
- ✅ Modelo `WorkflowUploadedFile` con relaciones
- ✅ Modelo `WorkflowExecution` actualizado
- ✅ Todos los casts y fillables definidos

**Tareas:**
- [x] Crear `WorkflowType.php`
- [x] Crear `WorkflowFileDefinition.php`
- [x] Crear `WorkflowRequiredColumn.php`
- [x] Crear `WorkflowFileBatch.php`
- [x] Crear `WorkflowUploadedFile.php`
- [x] Actualizar `WorkflowExecution.php`
- [x] Definir relaciones en todos los modelos

---

#### US-1.3: Como administrador, necesito datos iniciales del workflow Conciliación
**Puntos:** 3  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Workflow "Conciliación" creado
- ✅ 6 definiciones de archivo creadas
- ✅ Columnas requeridas para cada archivo definidas
- ✅ Seeder ejecutado sin errores

**Tareas:**
- [x] Crear `WorkflowTypeSeeder`
- [x] Definir workflow "Conciliación"
- [x] Definir 6 tipos de archivo
- [x] Definir columnas mínimas requeridas
- [x] Ejecutar seeder
- [x] Verificar datos en BD

---

#### US-1.4: Como desarrollador, necesito configuración del sistema
**Puntos:** 2  
**Prioridad:** Media

**Criterios de Aceptación:**
- ✅ Archivo `config/workflows.php` creado
- ✅ Variables de entorno en `.env.example`
- ✅ Documentación de configuración

**Tareas:**
- [x] Crear `config/workflows.php`
- [x] Agregar variables a `.env.example`
- [x] Documentar configuración

---

### Definición de Hecho (DoD)
- [x] Todas las migraciones ejecutadas
- [x] Todos los modelos creados con relaciones
- [x] Seeder ejecutado con datos de Conciliación
- [x] Configuración creada
- [x] Code review completado
- [x] Commit en rama `feature/workflows-sprint-1`

---

## 🔧 Sprint 2: Servicios Core
**Duración:** 2 semanas  
**Objetivo:** Implementar la lógica de negocio

### Historias de Usuario

#### US-2.1: Como sistema, necesito validar archivos cargados
**Puntos:** 8  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Servicio `FileValidationService` creado
- ✅ Valida cantidad de archivos (6)
- ✅ Identifica tipo por estructura de columnas
- ✅ Detecta archivos duplicados
- ✅ Detecta archivos faltantes
- ✅ Normaliza nombres de columnas
- ✅ Retorna errores descriptivos

**Tareas:**
- [ ] Crear `FileValidationService.php`
- [ ] Implementar `validateBatch()`
- [ ] Implementar `matchFileDefinition()`
- [ ] Implementar `checkRequiredColumns()`
- [ ] Implementar `normalize()`
- [ ] Crear tests unitarios
- [ ] Documentar servicio

---

#### US-2.2: Como sistema, necesito generar JSON para Python
**Puntos:** 5  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Servicio `WorkflowJsonGeneratorService` creado
- ✅ Lee todos los archivos Excel del batch
- ✅ Genera estructura `{Data: {archivo: [rows]}}`
- ✅ Incluye metadata (client, branch, workflow)
- ✅ Maneja archivos grandes (chunks)

**Tareas:**
- [ ] Crear `WorkflowJsonGeneratorService.php`
- [ ] Implementar `generateFromBatch()`
- [ ] Integrar Laravel Excel
- [ ] Implementar lectura por chunks
- [ ] Crear tests con archivos reales
- [ ] Documentar servicio

---

#### US-2.3: Como sistema, necesito ejecutar workflows
**Puntos:** 8  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Servicio `WorkflowExecutionService` creado
- ✅ Genera JSON del batch
- ✅ Envía a API Python (o mock)
- ✅ Guarda respuesta en BD
- ✅ Registra en `api_logs`
- ✅ Maneja errores correctamente
- ✅ Actualiza estados del batch

**Tareas:**
- [ ] Crear `WorkflowExecutionService.php`
- [ ] Implementar `execute()`
- [ ] Implementar `mockApiResponse()`
- [ ] Implementar `callExternalApi()`
- [ ] Crear tests con mock
- [ ] Documentar servicio

---

#### US-2.4: Como sistema, necesito generar PDFs del historial
**Puntos:** 5  
**Prioridad:** Media

**Criterios de Aceptación:**
- ✅ Servicio `WorkflowPdfService` creado
- ✅ Genera PDF de ejecución individual
- ✅ Incluye datos del cliente/sede
- ✅ Incluye archivos procesados
- ✅ Incluye resultado del workflow
- ✅ Diseño profesional

**Tareas:**
- [ ] Instalar `barryvdh/laravel-dompdf`
- [ ] Crear `WorkflowPdfService.php`
- [ ] Implementar `generateExecutionReport()`
- [ ] Crear template PDF
- [ ] Crear tests
- [ ] Documentar servicio

---

### Definición de Hecho (DoD)
- [ ] Todos los servicios implementados
- [ ] Tests unitarios pasando (>80% coverage)
- [ ] Documentación de servicios completa
- [ ] Code review completado
- [ ] Commit en rama `feature/workflows-sprint-2`

---

## 🎨 Sprint 3: Wizard de Carga
**Duración:** 2 semanas  
**Objetivo:** Interfaz de carga de archivos

### Historias de Usuario

#### US-3.1: Como Programador, necesito cargar archivos Excel
**Puntos:** 13  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Componente `WorkflowFileUploadWizard` creado
- ✅ Paso 1: Selección de cliente y sede
- ✅ Paso 2: Selección de workflow type
- ✅ Paso 3: Carga de 6 archivos
- ✅ Paso 4: Revisión y confirmación
- ✅ Validación en tiempo real
- ✅ Checklist visual de validaciones
- ✅ Barra de progreso
- ✅ Preview de JSON generado

**Tareas:**
- [ ] Crear componente Livewire `WorkflowFileUploadWizard`
- [ ] Implementar paso 1: Selección cliente/sede
- [ ] Implementar paso 2: Selección workflow
- [ ] Implementar paso 3: Carga de archivos
- [ ] Implementar paso 4: Confirmación
- [ ] Integrar `FileValidationService`
- [ ] Crear vista Blade
- [ ] Estilizar con diseño premium
- [ ] Crear tests de componente

---

#### US-3.2: Como Programador, necesito ver errores de validación claros
**Puntos:** 5  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Mensajes de error descriptivos
- ✅ Indicación de qué archivo tiene problema
- ✅ Indicación de qué columnas faltan
- ✅ Sugerencias de corrección
- ✅ Diseño visual claro (rojo/verde)

**Tareas:**
- [ ] Diseñar componente de errores
- [ ] Implementar mensajes descriptivos
- [ ] Agregar iconografía
- [ ] Crear tests de validación

---

### Definición de Hecho (DoD)
- [ ] Wizard funcional de 4 pasos
- [ ] Validación en tiempo real
- [ ] Mensajes de error claros
- [ ] Diseño premium implementado
- [ ] Tests de componente pasando
- [ ] Documentación de uso
- [ ] Code review completado
- [ ] Commit en rama `feature/workflows-sprint-3`

---

## ⚙️ Sprint 4: Ejecución e Historial
**Duración:** 2 semanas  
**Objetivo:** Ejecutar workflows y consultar historial

### Historias de Usuario

#### US-4.1: Como Programador, necesito ejecutar workflows
**Puntos:** 8  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Componente `WorkflowExecutionPanel` creado
- ✅ Muestra resumen del batch
- ✅ Botón "Ejecutar Workflow"
- ✅ Indicador de progreso durante ejecución
- ✅ Visualización de resultado
- ✅ Manejo de errores

**Tareas:**
- [ ] Crear componente `WorkflowExecutionPanel`
- [ ] Integrar `WorkflowExecutionService`
- [ ] Implementar UI de ejecución
- [ ] Implementar indicador de progreso
- [ ] Implementar vista de resultado
- [ ] Crear tests

---

#### US-4.2: Como Programador/Operador, necesito ver historial de ejecuciones
**Puntos:** 8  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Componente `WorkflowHistoryTable` creado
- ✅ Tabla con todas las ejecuciones
- ✅ Filtros por cliente, fecha, estado
- ✅ Columnas: Fecha, Cliente, Sede, Workflow, Usuario, Estado
- ✅ Botón "Ver Detalle"
- ✅ Botón "Descargar PDF"
- ✅ Paginación

**Tareas:**
- [ ] Crear componente `WorkflowHistoryTable`
- [ ] Implementar tabla con Livewire
- [ ] Implementar filtros
- [ ] Implementar paginación
- [ ] Implementar descarga PDF
- [ ] Crear tests

---

#### US-4.3: Como Programador, necesito ver JSON en /test
**Puntos:** 3  
**Prioridad:** Media

**Criterios de Aceptación:**
- ✅ Ruta `/test` creada
- ✅ Vista muestra últimos 10 batches
- ✅ Muestra JSON enviado
- ✅ Muestra JSON respuesta
- ✅ Diseño dark theme

**Tareas:**
- [ ] Crear ruta `/test`
- [ ] Crear controlador `WorkflowController`
- [ ] Crear vista `test.blade.php`
- [ ] Estilizar con dark theme
- [ ] Proteger con middleware

---

### Definición de Hecho (DoD)
- [ ] Panel de ejecución funcional
- [ ] Historial con filtros y paginación
- [ ] Vista /test implementada
- [ ] Descarga de PDF funcional
- [ ] Tests pasando
- [ ] Documentación completa
- [ ] Code review completado
- [ ] Commit en rama `feature/workflows-sprint-4`

---

## 🔧 Sprint 5: Configuración UI
**Duración:** 2 semanas  
**Objetivo:** Administración de workflows desde UI

### Historias de Usuario

#### US-5.1: Como Programador, necesito gestionar tipos de workflow
**Puntos:** 8  
**Prioridad:** Media

**Criterios de Aceptación:**
- ✅ Componente `WorkflowTypeManager` creado
- ✅ Lista de workflows existentes
- ✅ Crear nuevo workflow
- ✅ Editar workflow existente
- ✅ Activar/desactivar workflow
- ✅ Ver archivos requeridos por workflow

**Tareas:**
- [ ] Crear componente `WorkflowTypeManager`
- [ ] Implementar CRUD de workflows
- [ ] Crear formulario de workflow
- [ ] Implementar UI de gestión
- [ ] Crear tests

---

#### US-5.2: Como Programador, necesito editar columnas requeridas
**Puntos:** 13  
**Prioridad:** Media

**Criterios de Aceptación:**
- ✅ Componente `FileDefinitionEditor` creado
- ✅ Lista de columnas actuales
- ✅ Agregar nueva columna
- ✅ Eliminar columna
- ✅ Marcar como obligatoria/opcional
- ✅ Especificar tipo de dato
- ✅ Agregar descripción

**Tareas:**
- [ ] Crear componente `FileDefinitionEditor`
- [ ] Implementar lista de columnas
- [ ] Implementar modal "Agregar columna"
- [ ] Implementar eliminación de columna
- [ ] Implementar edición inline
- [ ] Crear tests

---

### Definición de Hecho (DoD)
- [ ] Gestión de workflows funcional
- [ ] Editor de columnas funcional
- [ ] UI premium implementada
- [ ] Tests pasando
- [ ] Documentación de uso
- [ ] Code review completado
- [ ] Commit en rama `feature/workflows-sprint-5`

---

## ✅ Sprint 6: Testing y Refinamiento
**Duración:** 2 semanas  
**Objetivo:** Asegurar calidad y optimizar

### Historias de Usuario

#### US-6.1: Como equipo, necesitamos tests completos
**Puntos:** 13  
**Prioridad:** Alta

**Criterios de Aceptación:**
- ✅ Tests unitarios de servicios (>80% coverage)
- ✅ Tests de integración de flujo completo
- ✅ Tests de componentes Livewire
- ✅ Tests de permisos
- ✅ Todos los tests pasando

**Tareas:**
- [ ] Crear tests de `FileValidationService`
- [ ] Crear tests de `WorkflowJsonGeneratorService`
- [ ] Crear tests de `WorkflowExecutionService`
- [ ] Crear tests de componentes Livewire
- [ ] Crear tests de permisos
- [ ] Ejecutar suite completa

---

#### US-6.2: Como usuario, necesito una experiencia optimizada
**Puntos:** 8  
**Prioridad:** Media

**Criterios de Aceptación:**
- ✅ Carga de archivos optimizada
- ✅ Generación de JSON eficiente
- ✅ UI responsiva y rápida
- ✅ Mensajes de carga apropiados
- ✅ Performance aceptable (<3s por operación)

**Tareas:**
- [ ] Optimizar lectura de Excel
- [ ] Implementar procesamiento asíncrono
- [ ] Agregar indicadores de carga
- [ ] Optimizar queries de BD
- [ ] Medir performance

---

#### US-6.3: Como equipo, necesitamos documentación completa
**Puntos:** 5  
**Prioridad:** Media

**Criterios de Aceptación:**
- ✅ README actualizado
- ✅ Documentación de API
- ✅ Guía de usuario
- ✅ Guía de desarrollo
- ✅ Troubleshooting guide

**Tareas:**
- [ ] Actualizar README principal
- [ ] Documentar endpoints
- [ ] Crear guía de usuario
- [ ] Crear guía de desarrollo
- [ ] Crear troubleshooting guide

---

### Definición de Hecho (DoD)
- [ ] Coverage de tests >80%
- [ ] Performance optimizada
- [ ] Documentación completa
- [ ] Bug fixing completado
- [ ] Code review final
- [ ] Merge a `main`
- [ ] Deploy a staging
- [ ] Validación con usuario final

---

## 📈 Métricas de Éxito

### Técnicas
- ✅ Coverage de tests >80%
- ✅ 0 bugs críticos
- ✅ Performance <3s por operación
- ✅ 100% de migraciones exitosas

### Funcionales
- ✅ Workflow "Conciliación" funcional end-to-end
- ✅ Validación de 6 archivos correcta
- ✅ Ejecución con mock API exitosa
- ✅ PDF generado correctamente
- ✅ Historial consultable

### Usuario
- ✅ Programador puede cargar archivos sin ayuda
- ✅ Operador puede consultar historial
- ✅ Mensajes de error claros y accionables
- ✅ UI intuitiva y profesional

---

## 🎯 Hitos Clave

| Hito | Fecha Estimada | Descripción |
|------|----------------|-------------|
| **M1: Base de Datos** | Semana 2 | Migraciones y modelos completos |
| **M2: Servicios Core** | Semana 4 | Lógica de negocio implementada |
| **M3: MVP Funcional** | Semana 6 | Carga y ejecución básica |
| **M4: Historial** | Semana 8 | Consulta de resultados |
| **M5: Configuración** | Semana 10 | Admin de workflows |
| **M6: Producción** | Semana 12 | Sistema completo en staging |

---

## 🚧 Riesgos y Mitigación

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Archivos Excel muy grandes | Media | Alto | Procesamiento por chunks |
| API Python no disponible | Alta | Medio | Usar mock API durante desarrollo |
| Estructura de columnas cambia | Media | Medio | Sistema configurable desde UI |
| Performance lenta | Media | Alto | Optimización temprana, tests de carga |
| Complejidad de validación | Baja | Medio | Tests exhaustivos, casos de prueba |

---

## 📋 Dependencias

### Externas
- Servidor Python para reglas de negocio (puede usar mock)
- Archivos Excel de ejemplo para testing

### Internas
- Laravel 10+
- Livewire 3+
- Laravel Excel
- DomPDF
- Spatie Permissions (ya instalado)

---

## 🔄 Retrospectivas

Después de cada sprint:
1. ¿Qué salió bien?
2. ¿Qué puede mejorar?
3. ¿Qué aprendimos?
4. Acciones para próximo sprint

---

## 📞 Equipo y Roles

- **Product Owner:** [TBD]
- **Scrum Master:** [TBD]
- **Desarrolladores:** [TBD]
- **QA:** [TBD]
- **Stakeholders:** Programador (usuario final), Operador (usuario final)

---

**Última actualización:** 2026-01-03  
**Versión:** 1.0.0  
**Estado:** Planificación
