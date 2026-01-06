# Dashboard Administrador

> **Estado:** ✅ OK (Mejorado en Sprint 3)  
> **Última actualización:** 2026-01-06

## Descripción

Panel principal para administradores/dueños de la consultoría. Muestra una visión global del sistema con énfasis en los **beneficios y ahorros** que genera la plataforma.

---

## Detalles Técnicos

| Elemento | Valor |
|----------|-------|
| **URL** | `/admin/dashboard` |
| **Ruta nombrada** | `admin.dashboard` |
| **Controlador** | `App\Http\Controllers\Admin\DashboardController@index` |
| **Vista** | `resources/views/admin/dashboard.blade.php` |
| **Layout** | `layouts/admin` |
| **Middleware** | `auth`, `role:Super Admin\|Manager` |

---

## Secciones de la Vista

### 1. Beneficios del Sistema (Nuevo)
Sección destacada que muestra:
- ⏱️ **Tiempo ahorrado** - Horas vs trabajo manual
- 📊 **Workflows ejecutados** - Cantidad del mes
- 💰 **Costo evitado** - Estimación en $
- 📈 **Productividad** - Promedio por operador
- 📊 **Barra de eficiencia** - % más rápido vs manual

### 2. Métricas Básicas
- Usuarios activos
- Clientes totales
- APIs disponibles
- Tipos de Workflow

### 3. Carga de Clientes por Usuario
Tabla con usuarios y cantidad de clientes asignados.

### 4. Actividad Reciente
- Workflows exitosos hoy
- Errores del día
- Operadores activos

---

## Datos del Controlador

El controlador pasa las siguientes variables a la vista:

```php
$stats = [
    'activeUsers' => User::where('is_active', true)->count(),
    'totalClients' => Client::count(),
    'totalApis' => ApiService::count(),
    // ... otras estadísticas
];

$usersWithClients = User::withCount('clients')->get();
```

---

## Permisos Requeridos

- Rol: `Super Admin` o `Manager`
- Acceso: Navegación lateral de administrador

---

## Notas

- Los datos de "Beneficios del Sistema" actualmente usan valores de ejemplo (`?? valor`)
- En futuro se conectarán a estadísticas reales basadas en workflows ejecutados