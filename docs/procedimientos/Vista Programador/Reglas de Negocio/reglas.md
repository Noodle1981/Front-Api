# Reglas de Negocio

> **Estado:** 🆕 IMPLEMENTAR (Sprint 4)  
> **Última actualización:** 2026-01-06

## Descripción

Módulo encargado de definir las reglas de validación y transformación de datos para los workflows. Actualmente se encuentra en proceso de rediseño para permitir una configuración dinámica.

---

## Detalles Técnicos (Planificados)

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/business-rules` |
| **Controlador** | `App\Http\Controllers\BusinessRuleController` |
| **Middleware** | `auth`, `role:Programador` |

---

## Objetivo del Rediseño

- **Editor Configurable:** Permitir al programador definir qué datos de entrada requiere cada regla mediante una interfaz visual.
- **Validación Dinámica:** Las reglas se ejecutarán en el servidor Python, enviando los parámetros configurados en Laravel.

---

## Tabla Relacionada

Actualmente existe la tabla `business_rules`, pero está bajo revisión para una posible migración hacia un sistema más flexible.

---

## Notas

- Esta sección es fundamental para el **Sprint 4** (Editor de Reglas).