# Vista de Clientes (Programador)

> **Estado:** ✅ OK  
> **Última actualización:** 2026-01-06

## Descripción

Vista informativa para que los programadores consulten la lista de clientes activos en el sistema. A diferencia del operador, esta es una vista de **solo lectura**.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/programadores/clients` |
| **Ruta nombrada** | `programmer.clients` |
| **Controlador** | `App\Http\Controllers\ClientController@index` (con lógica de filtrado de vista) |
| **Vista** | `resources/views/programmer/clients/index.blade.php` |
| **Layout** | `layouts/programmer` |
| **Middleware** | `auth`, `role:Programador` |

---

## Funcionalidades

- **Visualización:** Lista de empresas y sus contactos.
- **Búsqueda/Filtro:** Permite localizar clientes para asociar nuevos workflows.

---

## Modelo Relacionado

**Tabla:** `clients`

(Ver documentación de Clientes en Vista Operador para el detalle de la tabla).

---

## Permisos Requeridos

- Rol: `Programador`
- Acceso: Solo lectura de los datos básicos del cliente.