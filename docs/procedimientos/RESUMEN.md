# 📋 Resumen de Procedimientos - Front-API

> **Última actualización:** 2026-01-06  
> **Estado:** Documentación en progreso

---

## 🎯 Índice de Trabajo

| Categoría | Cantidad | Descripción |
|-----------|----------|-------------|
| 🗑️ ELIMINAR | 1 | Eliminar código y referencias |
| 🧹 LIMPIAR | 4 | Quitar referencias a automatizaciones |
| 🔄 REASIGNAR | 2 | Mover a rol correcto |
| ✅ OK | 6 | Funcionando correctamente |
| 🔧 MEJORAR | 2 | Ajustes menores necesarios |
| 🆕 IMPLEMENTAR | 4 | Features nuevos |
| ⏸️ STAND BY | 1 | Pendiente de dependencias |

---

## 👤 Vista Administrador

| Procedimiento | Estado | Acción | Detalles |
|---------------|--------|--------|----------|
| [Dashboard](Vista%20Administrador/Dasboard/dasboard.md) | 🔧 MEJORAR | Implementar métricas de ahorro de tiempo | Mostrar beneficios del sistema vs trabajo manual |
| [Emails](Vista%20Administrador/Emails/emails.md) | 🗑️ ELIMINAR | Eliminar toda la funcionalidad | Rutas, vistas, controladores, tablas |
| [Mantenimiento](Vista%20Administrador/Mantenimiento/mantenimiento.md) | 🔄 REASIGNAR | Mover a Programador | No es para el dueño |
| [Logs](Vista%20Administrador/logs/log.md) | 🔄 REASIGNAR | Mover a Programador | No es para el dueño |
| [Servicios API](Vista%20Administrador/Servicios%20APi/servicios.md) | ✅ OK | Solo lectura, catálogo informativo | Documentar técnicamente |
| [Usuarios](Vista%20Administrador/Usuarios/usuarios.md) | ✅ OK | CRUD de programadores y operadores | Documentar técnicamente |

---

## 👨‍💻 Vista Programador

| Procedimiento | Estado | Acción | Detalles |
|---------------|--------|--------|----------|
| [Dashboard](Vista%20Programador/Panel%20de%20Programador/dashboard.md) | 🧹 LIMPIAR | Quitar referencias a automatizaciones | Actualizar tablas |
| [Clientes](Vista%20Programador/Clientes/Clientes.md) | ✅ OK | Solo lectura | Documentar técnicamente |
| [Cargar Workflows](Vista%20Programador/Cargar%20workflows/workflows.md) | ✅ OK | Documentación técnica completa | Ya tiene detalles técnicos |
| [Historial Workflows](Vista%20Programador/Historial%20de%20Workflows/historialdeworkflows.md) | 🔧 MEJORAR | Arreglar preview del PDF | El batch funciona bien |
| [Monitor API](Vista%20Programador/Monitor%20Api/monitorapi.md) | 🧹 LIMPIAR | Quitar referencias a automatizaciones | Documentar técnicamente |
| [Reglas de Negocio](Vista%20Programador/Reglas%20de%20Negocio/reglas.md) | 🆕 IMPLEMENTAR | Editor con datos de entrada configurable | Crear sprint específico |
| [Testing](Vista%20Programador/Testing/testing.md) | ⏸️ STAND BY | Depende del servidor Python | |

---

## 👷 Vista Operador

| Procedimiento | Estado | Acción | Detalles |
|---------------|--------|--------|----------|
| [Dashboard](Vista%20Operador/Dasboard/dashboard.md) | 🧹 LIMPIAR | Quitar automatizaciones, actualizar tablas | |
| [Clientes](Vista%20Operador/Clientes/clientes.md) | ✅ OK | CRUD de clientes | Documentar técnicamente |
| [Historial Workflows](Vista%20Operador/Historial%20de%20workflows/historia.md) | 🆕 IMPLEMENTAR | Dar acceso a resultados de clientes | |
| [Monitor APIs](Vista%20Operador/Monitor%20Apis/monitor_apis.md) | 🧹 LIMPIAR + 🆕 | Quitar automatizaciones, crear vista "próximamente" | |
| [Solicitar Workflows](Vista%20Operador/Solicitar%20Workflows/solicitar.md) | 🆕 IMPLEMENTAR | Sistema de tickets/solicitudes | Comunicación con programador |

---

## 🚀 Plan de Sprints

### Sprint 1: Limpieza 🧹 ✅ COMPLETADO
> **Objetivo:** Eliminar todo lo relacionado a automatizaciones y emails

- [x] Eliminar rutas de emails (`/admin/email-settings`, `/admin/email-history`, `/admin/email-stats`)
- [x] Eliminar controladores de emails
- [x] Eliminar vistas de emails
- [x] Eliminar modelos y migraciones de emails
- [x] Limpiar Dashboard Programador (texto de automatizaciones)
- [x] Limpiar Dashboard Operador (texto de automatizaciones)
- [x] Limpiar Monitor API Programador
- [x] Limpiar Monitor APIs Operador
- [x] Eliminar tabla `email_logs` de la base de datos

### Sprint 2: Reorganización de Roles 🔄
> **Objetivo:** Asignar vistas al rol correcto

- [ ] Mover Mantenimiento de Admin → Programador
- [ ] Mover Logs de Admin → Programador
- [ ] Actualizar navegación/menús por rol
- [ ] Verificar permisos actualizados

### Sprint 3: Mejoras 🔧
> **Objetivo:** Arreglar funcionalidades existentes

- [ ] Arreglar preview PDF en Historial Workflows
- [ ] Diseñar dashboard Admin con métricas de ahorro

### Sprint 4: Features Nuevos 🆕
> **Objetivo:** Implementar nuevas funcionalidades

- [ ] Sistema de Solicitar Workflows (Operador → Programador)
- [ ] Acceso a Historial Workflows para Operador
- [ ] Vista "Próximamente" para Monitor APIs Operador
- [ ] Editor de Reglas de Negocio configurable (requiere análisis)

---

## 📦 Auditoría de Tablas ✅ COMPLETADA

| Tabla | Acción | Estado |
|-------|--------|--------|
| `email_logs` | 🗑️ ELIMINADA | ✅ Migración ejecutada |
| `business_rules` | ⚠️ REVISAR | Tabla legacy, posible eliminación futura |
| `workflows` | ⚠️ REVISAR | Tabla legacy, posible eliminación futura |

---

## 📝 Pendientes de Documentación Técnica

Cada archivo marcado con "Documentar técnicamente" necesita:
- [ ] URL de la ruta
- [ ] Controlador responsable
- [ ] Componentes Livewire (si aplica)
- [ ] Tablas utilizadas
- [ ] Permisos requeridos

---

## ✅ Orden de Trabajo Recomendado

1. **Completar documentación técnica** de cada procedimiento
2. **Ejecutar Sprint 1** (Limpieza) - Lo más urgente
3. **Ejecutar Sprint 2** (Roles) - Orden lógico
4. **Auditoría de tablas** - Para eliminar lo que sobra
5. **Ejecutar Sprint 3** (Mejoras)
6. **Ejecutar Sprint 4** (Features) - Cuando esté limpio

---

> **Nota:** Este documento es una guía viva. Actualizar conforme se avance.
