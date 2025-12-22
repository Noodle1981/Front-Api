---
description: Contexto completo del proyecto Front-API para cualquier IA o desarrollador
---

# 🚀 Front-API - Contexto del Proyecto

> **Sistema SaaS de Gestión de Clientes e Integraciones API**  
> Última actualización: Diciembre 2024 | Versión: 2.0.0

---

## 📋 Descripción General

**Front-API** es un sistema completo de administración SaaS para:
- Gestión de clientes (CRUD con jerarquías sede/sucursal)
- Integraciones con APIs externas (Mercado Pago, AFIP, Ualá, Naranja X)
- Monitoreo de transacciones y ejecución de APIs
- Sistema de alertas automatizadas por email
- Reglas de negocio ETL con Python (Pyodide) para procesamiento de datos

**Propósito**: Permitir a programadores crear integraciones API y workflows, mientras operadores ejecutan estos workflows predefinidos para sus clientes.

---

## 🛠️ Stack Tecnológico

### Backend
| Tecnología | Versión | Uso |
|------------|---------|-----|
| PHP | 8.2+ | Lenguaje principal |
| Laravel | 12.x | Framework principal |
| Spatie/Laravel-Permission | 6.24 | Sistema RBAC de roles y permisos |
| Laravel Breeze | 2.3 | Autenticación |
| Livewire | 3.7 | Componentes reactivos |
| Laravel Sanctum | 4.0 | Autenticación API |
| Pest | 3.8 | Testing |

### Frontend
| Tecnología | Versión | Uso |
|------------|---------|-----|
| Tailwind CSS | 4.x | Estilos (Glassmorphism design) |
| Alpine.js | 3.x | Interactividad JavaScript |
| Chart.js | 4.4.0 | Gráficos y visualizaciones |
| Monaco Editor | - | Editor de código Python |
| Pyodide | - | Ejecución Python en navegador |
| Vite | 7.0 | Build tool |

### Base de Datos
| Tecnología | Versión | Uso |
|------------|---------|-----|
| MySQL | 8.0+ | Base de datos principal |
| SQLite | - | Testing |

---

## 🗂️ Estructura del Proyecto

```
Front-Api/
├── app/
│   ├── Console/Commands/     # Comandos Artisan (ResetDemo, etc.)
│   ├── Http/
│   │   ├── Controllers/      # Controladores principales
│   │   │   ├── Admin/        # Panel administrativo
│   │   │   ├── Api/          # Endpoints REST
│   │   │   ├── Auth/         # Autenticación Breeze
│   │   │   └── Programmer/   # Módulo programador (Integraciones, Business Rules)
│   ├── Livewire/             # Componentes Livewire
│   ├── Models/               # Modelos Eloquent
│   ├── Mail/                 # Mailables
│   ├── Policies/             # Authorization policies
│   └── Services/             # Servicios de negocio
├── database/
│   ├── migrations/           # Migraciones
│   └── seeders/              # Seeders de datos
├── resources/
│   └── views/                # Vistas Blade
├── routes/
│   ├── web.php               # Rutas principales
│   ├── api.php               # Rutas API REST
│   └── auth.php              # Rutas autenticación
├── tests/
│   ├── Feature/              # Tests funcionales
│   └── Unit/                 # Tests unitarios
└── docs/
    └── TEST_DOCUMENTATION.md # Documentación de tests
```

---

## 👥 Sistema de Roles (RBAC)

| Rol | Descripción | Rutas Principales |
|-----|-------------|-------------------|
| **Super Admin** | Acceso total al sistema | `/admin/*` |
| **Manager** | Gestión administrativa (sin config técnica) | `/admin/*` |
| **Programador** | Crea APIs, endpoints, reglas de negocio ETL | `/programadores/*` |
| **Operador** | Ejecuta workflows, gestiona sus clientes | `/dashboard`, `/clients` |

### Permisos por Rol

| Permiso | Super Admin | Manager | Programador | Operador |
|---------|-------------|---------|-------------|----------|
| view clients | ✅ | ✅ | ✅ | ✅ |
| create clients | ✅ | ✅ | ✅ | ✅ |
| delete clients | ✅ | ✅ | ❌ | ❌ |
| reassign clients | ✅ | ✅ | ✅ | ❌ |
| manage api catalog | ✅ | ❌ | ❌ | ❌ |
| manage users | ✅ | ❌ | ❌ | ❌ |

---

## 🔗 Rutas Principales

### Públicas
- `/` - Landing page
- `/login` - Inicio de sesión
- `/register` - Registro

### Operador (Usuarios)
- `/dashboard` - Dashboard principal
- `/clients` - Gestión de clientes (CRUD)
- `/api-dashboard` - Monitor de APIs

### Programador
- `/programadores/dashboard` - Dashboard KPIs
- `/programadores/enterprise` - Gestión de integraciones API
- `/programadores/services/{id}/endpoints` - Gestor de endpoints
- `/programadores/clients` - Clientes (vista avanzada)
- `/programadores/reglas` - Reglas de negocio ETL
- `/programadores/reglas/create` - Workflow Builder (Python + Monaco)
- `/programadores/api-dashboard` - Monitor de APIs

### Administrador
- `/admin/dashboard` - Panel administrativo
- `/admin/users` - Gestión de usuarios
- `/admin/api-services` - Catálogo de APIs
- `/admin/email-settings` - Configuración SMTP
- `/admin/email-history` - Historial de emails
- `/admin/maintenance` - Mantenimiento del sistema

---

## 🗄️ Modelos Principales

| Modelo | Tabla | Descripción |
|--------|-------|-------------|
| `User` | `users` | Usuarios del sistema |
| `Client` | `clients` | Clientes (sedes/sucursales con `parent_id`) |
| `ClientCredential` | `client_credentials` | Credenciales API por cliente |
| `ApiService` | `api_services` | Catálogo de servicios API |
| `Endpoint` | `endpoints` | Endpoints por servicio API |
| `ApiLog` | `api_logs` | Registro de ejecuciones API |
| `Transaction` | `transactions` | Transacciones exitosas |
| `BusinessRule` | `business_rules` | Reglas de negocio ETL |
| `Workflow` | `workflows` | Workflows compuestos |
| `EmailLog` | `email_logs` | Historial de emails enviados |

---

## 📊 Estado Actual del Roadmap

### ✅ ETAPA 1 - Programadores (Completado)
- [x] Panel de Programadores (`/programadores/dashboard`)
- [x] Enterprise Module (Gestión de integraciones)
- [x] Endpoints Manager (`/programadores/services/{id}/endpoints`)
- [x] Reglas de Negocio ETL con Pyodide + Monaco Editor
- [x] Workflow Builder integrado

### 🔄 ETAPA 2 - Operadores (En Progreso)
- [ ] Dashboard Operador actualizado
- [ ] Vista `/operadores/workflow` para ejecutar workflows predefinidos
- [ ] Sistema de ejecución de reglas sin programación

### ✅ ETAPA 3 - Livewire (Instalado)
- [x] Livewire v3.7 instalado
- [x] Componente `BuscadorClientes` creado
- [ ] Dashboard con actualización automática
- [ ] Formularios de reglas en tiempo real
- [ ] Notificaciones en vivo

---

## 🧪 Testing

### Framework: Pest PHP 3.8

```bash
# Ejecutar todos los tests
php artisan test

# Tests de roles específicos
php artisan test --filter=LoginByRoleTest

# Tests de permisos
php artisan test --filter=RoleMatrixTest

# Con salida detallada
php artisan test -v
```

### Configuración
- Base de datos: SQLite (`database/testing.sqlite`)
- Trait: `LazilyRefreshDatabase` (aplicado globalmente)

### Suites de Tests
| Suite | Tests | Descripción |
|-------|-------|-------------|
| LoginByRoleTest | 34 | Acceso por rol |
| RoleMatrixTest | 5 | Matriz de permisos |
| RoleAccessTest | 8 | Acceso a rutas |
| AuthenticationTest | 4 | Login/Logout |
| CredentialFlowTest | 3 | Flujo de credenciales |

---

## ⚡ Instalación Rápida

```bash
# 1. Clonar repositorio
git clone https://github.com/Noodle1981/Front-Api.git
cd Front-Api

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Configurar .env (MySQL)
# DB_DATABASE=front_api
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Ejecutar migraciones y seeders básicos
php artisan migrate --seed

# 6. (Opcional) Cargar datos de demo completos
php artisan db:seed --class=CompleteDemoSeeder

# 7. Compilar assets
npm run dev

# 8. Iniciar servidor
php artisan serve
# Acceder a: http://127.0.0.1:8000
```

### Comando de Reset Demo
```bash
php artisan app:reset-demo
# Ejecuta migrate:fresh + secuencia correcta de seeders
```

---

## 👤 Usuarios de Demo

| Email | Password | Rol | Datos |
|-------|----------|-----|-------|
| `admin@example.com` | `password` | Super Admin | Acceso total |
| `user@example.com` | `password` | Operador | 5 Sedes, 4 Sucursales (ideal presentaciones) |
| `analista@example.com` | `password` | Programador | Vista global |
| `maria.gonzalez@demo.com` | `password123` | Operador | 2 sedes |
| `carlos.rodriguez@demo.com` | `password123` | Operador | 2 sedes |

---

## 📐 Convenciones de Código

### Nombrado
- **Controladores**: PascalCase, singular (`ClientController`, `ApiServiceController`)
- **Modelos**: PascalCase, singular (`Client`, `ApiService`)
- **Vistas**: kebab-case en carpetas por módulo (`programmer/dashboard.blade.php`)
- **Rutas**: kebab-case, agrupadas por prefijo (`/programadores/api-dashboard`)
- **Migraciones**: snake_case con timestamp

### Organización por Rol
- `app/Http/Controllers/Admin/` → Super Admin, Manager
- `app/Http/Controllers/Programmer/` → Programador
- `app/Http/Controllers/` → Compartido o Operador

### Vistas Blade
- Layout principal: `layouts/app.blade.php`
- Componentes Blade en: `resources/views/components/`
- Componentes Livewire en: `app/Livewire/`

### Diseño UI
- **Glassmorphism**: `backdrop-blur-md`, transparencias, gradientes
- **Dark Theme**: Tema oscuro con acentos de color
- **Responsive**: Mobile-first con Tailwind breakpoints

---

## 🔀 Convenciones Git

### Ramas
- `main` - Producción estable
- `develop` - Desarrollo activo
- `feature/nombre-feature` - Features nuevas
- `fix/descripcion-bug` - Correcciones

### Commits
```bash
# Ejemplos de mensajes de commit
feat: add endpoint testing panel
fix: resolve client pagination issue
docs: update README with new routes
refactor: extract API service logic
test: add role matrix tests
```

### Flujo
1. Fork del proyecto
2. Crear rama `git checkout -b feature/AmazingFeature`
3. Commit cambios `git commit -m 'feat: add AmazingFeature'`
4. Push `git push origin feature/AmazingFeature`
5. Abrir Pull Request

---

## 📧 Configuración Email (SMTP)

```env
# Gmail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-app-password
MAIL_ENCRYPTION=tls

# Mailtrap (Testing)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

Después de cambios: `php artisan config:clear`

---

## 📚 Documentación Clave

| Archivo | Ubicación | Contenido |
|---------|-----------|-----------|
| README.md | `/README.md` | Documentación principal |
| ROADMAP.MP | `/ROADMAP.MP` | Estado del roadmap |
| TEST_DOCUMENTATION.md | `/docs/TEST_DOCUMENTATION.md` | Documentación de tests |
| phpunit.xml | `/phpunit.xml` | Configuración PHPUnit |
| composer.json | `/composer.json` | Dependencias PHP |
| package.json | `/package.json` | Dependencias NPM |

---

## 🛟 Comandos Útiles

```bash
# Desarrollo
composer dev                  # Server + Queue + Pail + Vite concurrente
php artisan serve             # Solo servidor
npm run dev                   # Solo Vite

# Base de datos
php artisan migrate           # Ejecutar migraciones
php artisan migrate:fresh     # Reset + migrate
php artisan db:seed           # Ejecutar seeders
php artisan app:reset-demo    # Reset demo completo

# Cache
php artisan config:clear      # Limpiar config cache
php artisan cache:clear       # Limpiar application cache
php artisan view:clear        # Limpiar compiled views

# Testing
php artisan test              # Ejecutar todos los tests
php artisan test --filter=X   # Filtrar por nombre

# Utilidades
php artisan tinker            # REPL de Laravel
php artisan route:list        # Listar rutas
php artisan make:controller   # Crear controlador
php artisan make:model        # Crear modelo
php artisan make:livewire     # Crear componente Livewire
```

---

## ⚠️ Notas Importantes

1. **Credenciales API**: Se almacenan encriptadas en `client_credentials.credentials`
2. **Soft Deletes**: Los clientes usan eliminación lógica
3. **Jerarquías**: Los clientes pueden ser sedes (`parent_id = null`) o sucursales (`parent_id = sede_id`)
4. **Pyodide**: Se ejecuta en el navegador, no requiere instalación Python en servidor
5. **Roles renombrados**: Analista → Programador, Usuario → Operador

---

**Desarrollado por**: Omar Olivera ([@Noodle1981](https://github.com/Noodle1981))  
**Repositorio**: [github.com/Noodle1981/Front-Api](https://github.com/Noodle1981/Front-Api)
