# Testing de Workflows

> **Estado:** ⏸️ STAND BY  
> **Última actualización:** 2026-01-06

## Descripción

Herramienta de depuración para probar workflows antes de su despliegue final. Permite enviar archivos de prueba y recibir la respuesta JSON directa del servidor de procesamiento.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/workflows/test` |
| **Ruta nombrada** | `programmer.workflows.test` |
| **Controlador** | `App\Http\Controllers\WorkflowController@testView` |
| **Vista** | `resources/views/programmer/test.blade.php` |
| **Middleware** | `auth`, `role:Programador` |

---

## Estado Actual

El módulo está en **stand-by** esperando la integración completa con el servidor de procesamiento de Python para mostrar resultados en tiempo real con mayor detalle técnico.

---

## Notas

- Solo usuarios con rol **Programador** pueden acceder a esta vista.
