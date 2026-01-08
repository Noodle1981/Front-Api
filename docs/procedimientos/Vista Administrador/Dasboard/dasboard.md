# Dashboard Administrador

> **Ruta:** `/admin/dashboard`  
> **Acceso:** Super Admin, Manager  
> **Última actualización:** 2026-01-08

---

## Descripción

Panel principal para administradores. Visión global del sistema con métricas de usuarios, clientes y workflows.

---

## Acceso

1. Iniciar sesión como **Super Admin** o **Manager**
2. Redirección automática desde `/dashboard`
3. O menú lateral → **Dashboard**

---

## Secciones de la Vista

### 1. Métricas Principales (Cards)

| Métrica | Descripción |
|---------|-------------|
| **Usuarios Activos** | Total de usuarios con acceso |
| **Clientes** | Total de clientes en el sistema |
| **Workflows Ejecutados** | Total de ejecuciones completadas |
| **Tipos de Workflow** | Cantidad de workflows configurados |

### 2. Distribución por Rol

Gráfico o tabla mostrando:
- Cantidad de Super Admins
- Cantidad de Managers
- Cantidad de Programadores
- Cantidad de Operadores

### 3. Carga de Clientes por Usuario

Tabla con:
- Nombre del usuario
- Rol asignado
- Cantidad de clientes asignados
- Última actividad

### 4. Actividad Reciente

Últimos eventos del sistema:
- Nuevos usuarios creados
- Workflows ejecutados
- Clientes agregados

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| Controlador | `Admin\DashboardController@index` |
| Vista | `admin/dashboard.blade.php` |
| Middleware | `auth`, `role:Super Admin|Manager` |

---

## Navegación Disponible

| Destino | Ruta |
|---------|------|
| Dashboard | `/admin/dashboard` |
| Usuarios | `/admin/users` |
| Perfil | `/profile` |
