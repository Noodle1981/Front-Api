# Front-API - Sistema de Gestión de Clientes y APIs

Sistema completo de administración SaaS para gestión de clientes, integraciones API, monitoreo de transacciones y alertas automatizadas.

## 🚀 Características Principales

### 1. Gestión de Clientes (CRUD Avanzado)
- **Jerarquías**: Soporte para Sedes Centrales y Sucursales con relación `parent_id`
- **Datos Completos**: CUIT, Razón Social, Nombre de Fantasía, Condición Fiscal, Rubro, Empleados
- **Información de Contacto**: Email, Teléfono, Sitio Web, Dirección completa (Ciudad, Provincia, CP)
- **Estados**: Activación/Desactivación con razones (Falta de Pago, Otros)
- **Transferencia**: Los Analistas pueden transferir clientes entre contadores
- **Notas Internas**: Campo para observaciones del equipo

### 2. Seguridad y Roles (RBAC) 🛡️
Implementado con `spatie/laravel-permission`:
- **Super Admin**: Acceso total + Panel de Administración 
- **Programador (Ex Analista)**: Gestión avanzada de Integraciones, Endpoints, Clientes y reportes.
- **Operador (Ex User)**: Gestión de sus propios clientes y ejecución de workflows.
 
### 3. Catálogo de APIs y Endpoints 🔌
- **Wizard de Integración**: Interfaz paso a paso para configurar Mercado Pago, Ualá, etc.
- **Gestión de Endpoints**: Alta de endpoints (GET/POST) con parámetros.
- **Testing en Vivo**: Panel integrado para probar endpoints reales directamente desde la UI.
- **Credenciales Dinámicas**: Soporte para OAuth, Tokens, Keys según el proveedor.
- **Configuración por Cliente**: Cada cliente puede tener múltiples APIs configuradas.

### 4. Sistema de Reglas de Negocio ETL 🐍
- **Workflow Builder**: Editor visual de 3 paneles (Entrada/Editor Python/Resultado)
- **Motor Python (Pyodide)**: Ejecución de código Python directamente en el navegador para testing
- **Editor Monaco**: Editor de código profesional (VS Code) integrado
- **Tipos de Reglas**: Extracción, Transformación y Visualización
- **Integración Enterprise**: Vinculación con clientes, APIs y endpoints
- **Diseño Glassmorphism**: Interfaz moderna con efectos de vidrio esmerilado

### 5. Dashboards Analíticos 📊

#### Dashboard de Analista (`/analistas/dashboard`)
- **KPIs por Usuario**: Error rate, % automatización, última actividad, carga de trabajo
- **Sistema de Alertas**: Críticas (>10% errores), Warnings (inactividad >7 días), Info (<30% automatización)
- **Rankings**: Top 3 más eficientes y más automatizados
- **Gráfico de Tendencia**: Errores de los últimos 7 días con Chart.js
- **Acciones Rápidas**: Links directos a dashboard API y clientes filtrados por usuario

#### Dashboard API (`/analistas/api-dashboard`)
- **Live Feed Paginado**: Últimas 15 ejecuciones con usuario/contador
- **Filtros Avanzados**: Por cliente, usuario, servicio, estado
- **Estadísticas de Automatización**: APIs automatizadas vs manuales
- **Selector de Contexto**: Filtrar toda la vista por un contador específico

#### Vista de Clientes (`/analistas/clients`)
- **Estadísticas Completas**: Total, Activos, Inactivos, Sedes, Sucursales, Con APIs
- **Filtro por Usuario**: Ver clientes de un contador específico
- **Columna Responsable**: Muestra el contador asignado
- **Razones de Desactivación**: Badge con motivo (Falta de Pago, Otros)

### 5. Sistema de Alertas por Email 📧

#### Configuración (`/admin/email-settings`)
- **Información SMTP**: Muestra configuración actual del servidor
- **Email de Prueba**: Verificar que el SMTP funciona correctamente
- **Plantillas HTML**: Diseños profesionales con gradientes

#### Historial (`/admin/email-history`)
- **Registro Completo**: Todos los emails enviados con estado
- **Filtros**: Por tipo (Prueba, Error API, Sistema), estado (Enviado, Fallido), fechas
- **Paginación**: 20 registros por página
- **Detalles de Error**: Tooltip con mensaje de error en fallidos

#### Estadísticas (`/admin/email-stats`)
- **Métricas**: Total enviados, exitosos, fallidos, tasa de éxito
- **Gráfico de Tendencia**: Emails por día (últimos 30 días)
- **Distribución por Tipo**: Gráfico de dona con tipos de email

### 6. Monitoreo de APIs y Transacciones 📈
- **API Logs**: Registro de todas las ejecuciones (endpoint, método, status, tiempo de respuesta)
- **Transacciones**: Registro de operaciones exitosas (tipo, monto, moneda, estado)
- **Histórico**: 30 días de datos para análisis de tendencias

### 7. Interfaz Moderna (Glassmorphism Design) 🎨
- **Glassmorphism**: Efecto de vidrio con `backdrop-blur-md` y transparencias
- **Gradientes**: Cards con gradientes sutiles y bordes luminosos
- **Hover Effects**: Transiciones suaves en todas las tarjetas
- **Responsive**: Diseño adaptable a móvil, tablet y desktop
- **Dark Theme**: Tema oscuro profesional con acentos de color

---

## 🛠️ Instalación y Configuración

### Requisitos
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- Git

### Instalación

1. **Clonar el repositorio**:
```bash
git clone https://github.com/Noodle1981/Front-Api.git
cd Front-Api
```

2. **Instalar dependencias**:
```bash
composer install
npm install
```

3. **Configurar entorno**:
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos en `.env`**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=front_api
DB_USERNAME=root
DB_PASSWORD=
```

5. **Ejecutar migraciones y seeders**:
```bash
php artisan migrate --seed
```

Esto creará:
- Roles y permisos
- Usuario Super Admin: `admin@example.com` / `password`
- Servicios API (AFIP, Mercado Pago)

6. **Generar datos de demo** (Opcional):
```bash
php artisan db:seed --class=CompleteDemoSeeder
```

Esto creará:
- 5 usuarios contadores con emails `*.demo.com`
- 10+ clientes con datos completos (sedes y sucursales)
- Credenciales API configuradas
- 30 días de logs y transacciones

8. **Resetear Demo** (Comando Personalizado):
    ```bash
    php artisan app:reset-demo
    ```
    > Este comando ejecuta automáticamente `migrate:fresh` y la secuencia correcta de seeders para restaurar el entorno de demostración.

9. **Compilar assets**:
```bash
npm run build
# O para desarrollo:
npm run dev
```

8. **Iniciar servidor**:
```bash
php artisan serve
```

Acceder a: `http://127.0.0.1:8000`

---

## 📧 Configuración de Email (SMTP)

Para que el sistema de alertas funcione, configurar en `.env`:

### Opción 1: Gmail
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

> **Nota**: Para Gmail necesitas crear una "Contraseña de Aplicación" en tu cuenta de Google.

### Opción 2: Mailtrap (Testing)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu-mailtrap-user
MAIL_PASSWORD=tu-mailtrap-pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@front-api.com
MAIL_FROM_NAME="${APP_NAME}"
```

Después de configurar:
```bash
php artisan config:clear
```

---

## 🗄️ Estructura de Base de Datos

### Tablas Principales

#### `users`
- Usuarios del sistema (Super Admin, Manager, Analista, User/Contador)
- Relación con `clients` (1:N)

#### `clients`
- Clientes con información completa
- `parent_id` para jerarquía (sede/sucursal)
- `user_id` para asignar contador responsable
- `deactivation_reason` para motivo de desactivación

#### `client_credentials`
- Credenciales API por cliente
- `credentials` encriptado (JSON)
- `execution_frequency` (daily, weekly)
- `alert_email` para alertas específicas

#### `api_services`
- Catálogo de servicios disponibles (AFIP, Mercado Pago, etc.)
- `required_fields` define qué campos necesita cada API

#### `api_logs`
- Registro de todas las ejecuciones API
- Endpoint, método, status, tiempo de respuesta
- Payloads de request/response
- Mensajes de error

#### `transactions`
- Transacciones exitosas
- Tipo, monto, moneda, estado
- Relación con cliente y servicio API

#### `email_logs`
- Historial de emails enviados
- Tipo (test, api_error, system)
- Estado (sent, failed)
- Metadata en JSON

---

php artisan app:reset-demo ## para generar datos de demo

## 🎯 Usuarios de Demo

Después de ejecutar `CompleteDemoSeeder`:

| Email | Password | Rol | Datos de Demo |
|-------|----------|-----|---------------|
| `user@example.com` | `password` | User (Contador) | 5 Sedes, 4 Sucursales, Gráficos completos (Ideal Presentaciones) |
| `analista@example.com` | `password` | Analista (Inspector) | Vista global de todos los contadores |
| `admin@example.com` | `password` | Super Admin | Acceso total al sistema |
| `maria.gonzalez@demo.com` | `password123` | User | 2 sedes, 2 sucursales |
| `carlos.rodriguez@demo.com` | `password123` | User | 2 sedes, 1 sucursal |
| `ana.martinez@demo.com` | `password123` | User | 1 sede, 2 sucursales |
| `roberto.fernandez@demo.com` | `password123` | User | 2 sedes |
| `laura.sanchez@demo.com` | `password123` | User | 2 sedes, 1 sucursal |

---

## 🚀 Características Técnicas

### Backend
- **Framework**: Laravel 11
- **Autenticación**: Laravel Breeze
- **Permisos**: Spatie Laravel Permission
- **Base de Datos**: MySQL con migraciones
- **Encriptación**: Credenciales API encriptadas
- **Soft Deletes**: Eliminación lógica de clientes

### Frontend
- **CSS Framework**: Tailwind CSS 4
- **JavaScript**: Alpine.js para interactividad
- **Gráficos**: Chart.js 4.4.0
- **Iconos**: Font Awesome 6
- **Diseño**: Glassmorphism con gradientes

### APIs y Servicios
- **Email**: Sistema completo de alertas con historial
- **Webhooks**: Receptor para notificaciones externas
- **Cron Jobs**: Automatización programada

---

## 📊 Rutas Principales

### Públicas
- `/` - Landing page
- `/login` - Inicio de sesión
- `/register` - Registro (si está habilitado)

### Usuario (Contador)
- `/dashboard` - Dashboard principal
- `/clients` - Gestión de clientes
- `/clients/{id}` - Detalle de cliente
- `/api-dashboard` - Monitor de APIs

### Programador
- `/programadores/dashboard` - Dashboard principal
- `/programadores/enterprise` - Gestión de Integraciones (Enterprise)
- `/programadores/services/{id}/endpoints` - Gestor de Endpoints y Pruebas
- `/programadores/clients` - Gestión de Clientes Avanzada
- `/programadores/reglas` - Reglas de Negocio ETL
- `/programadores/reglas/create` - Workflow Builder (nuevo/editar reglas)

### Admin
- `/admin/dashboard` - Panel de administración
- `/admin/users` - Gestión de usuarios
- `/admin/api-services` - Catálogo de APIs
- `/admin/email-settings` - Configuración de email
- `/admin/email-history` - Historial de emails
- `/admin/email-stats` - Estadísticas de emails
- `/admin/maintenance` - Mantenimiento del sistema

---

## 🧪 Testing

```bash
php artisan test
```

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crear rama de feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

---

## 📝 Licencia

Este proyecto es privado y está bajo licencia propietaria.

---

## 👨‍💻 Desarrollado por

**Omar Olivera (Noodle1981)**
- GitHub: [@Noodle1981](https://github.com/Noodle1981)
- Proyecto: Front-API

---

## 🛟 Soporte

Para reportar bugs o solicitar features, crear un issue en GitHub.

---

**Última actualización**: Diciembre 2024
**Versión**: 2.0.0