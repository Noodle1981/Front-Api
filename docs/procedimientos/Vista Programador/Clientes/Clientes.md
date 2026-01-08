# Gestión de Clientes (Programador)

> **Ruta:** `/programadores/clients`  
> **Acceso:** Programador  
> **Última actualización:** 2026-01-08

---

## Descripción

Vista para gestionar clientes desde la perspectiva del programador. Permite ver, crear, editar clientes y sus sucursales, así como transferir clientes a otros programadores.

---

## Acceso

1. Iniciar sesión como **Programador**
2. Menú lateral → **Clientes**

---

## Funcionalidades

### 1. Lista de Clientes

| Columna | Descripción |
|---------|-------------|
| Empresa | Nombre de la empresa cliente |
| Contacto | Nombre del contacto principal |
| Email | Email de contacto |
| Sucursales | Cantidad de sucursales/sedes |
| Estado | Activo / Inactivo |
| Acciones | Ver, Editar, Transferir |

### 2. Ver Cliente

**Ruta:** `/programadores/clients/{id}`

Muestra detalle completo del cliente:
- Datos de la empresa
- Información de contacto
- Lista de sucursales (branches)
- Historial de workflows ejecutados

### 3. Crear/Editar Cliente

**Rutas:** 
- Crear: `/programadores/clients/create`
- Editar: `/programadores/clients/{id}/edit`

Campos:
- Nombre de empresa
- Contacto (nombre, email, teléfono)
- Dirección
- Notas

### 4. Gestión de Sucursales

Desde la vista del cliente se pueden:
- Agregar nuevas sucursales
- Editar sucursales existentes
- Activar/desactivar sucursales

### 5. Transferir Cliente

**Ruta:** `/programadores/clients/{id}/transfer`

Permite transferir un cliente a otro programador:
- Seleccionar programador destino
- Confirmar transferencia
- El cliente pasa a la gestión del nuevo programador

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| Controlador | `ClientController` |
| Controlador Transfer | `ClientTransferController` |
| Modelo | `Client` |
| Modelo Sucursales | `Branch` |

---

## Rutas Disponibles

| Acción | Ruta | Método |
|--------|------|--------|
| Listar | `/programadores/clients` | GET |
| Ver | `/programadores/clients/{id}` | GET |
| Crear | `/programadores/clients/create` | GET |
| Guardar | `/programadores/clients` | POST |
| Editar | `/programadores/clients/{id}/edit` | GET |
| Actualizar | `/programadores/clients/{id}` | PUT |
| Transferir | `/programadores/clients/{id}/transfer` | GET/PUT |

---

## Permisos

| Acción | Permitido |
|--------|-----------|
| Ver clientes | ✅ |
| Crear clientes | ✅ |
| Editar clientes | ✅ |
| Eliminar clientes | ❌ (Solo Admin) |
| Transferir clientes | ✅ |
