# 🧪 Documentación de Tests - Front-Api

> Última ejecución: {{ fecha }}  
> Estado: ✅ Tests de Roles funcionando correctamente

---

## 📋 Resumen de Suites de Tests

| Suite | Tests | Estado | Descripción |
|-------|-------|--------|-------------|
| LoginByRoleTest | 34 | ✅ PASS | Acceso por rol al sistema |
| RoleMatrixTest | 5 | ✅ PASS | Matriz de permisos por rol |
| RoleAccessTest | 8 | ✅ PASS | Acceso a rutas por rol |
| AuthenticationTest | 4 | ✅ PASS | Login/Logout básico |
| RegistrationTest | 2 | ✅ PASS | Registro de usuarios |
| CredentialFlowTest | 3 | ✅ PASS | Flujo de credenciales |
| BranchIsolationTest | 1 | ✅ PASS | Aislamiento sede/sucursal |

---

## 🔐 Tests de Login por Rol

### Archivo: `tests/Feature/LoginByRoleTest.php`

Verifica que cada rol accede únicamente a las rutas correspondientes.

#### Super Admin (5 tests)
- ✅ Login redirige a admin dashboard
- ✅ Puede acceder al panel admin
- ✅ Puede acceder a gestión de usuarios
- ✅ Puede acceder a API services
- ✅ Puede acceder a email settings

#### Manager (4 tests)
- ✅ Login redirige a admin dashboard
- ✅ Puede acceder al panel admin
- ✅ Puede acceder a gestión de usuarios
- ✅ NO puede acceder a rutas programador (403)

#### Programador (8 tests)
- ✅ Login redirige a programmer dashboard
- ✅ Puede acceder al dashboard programador
- ✅ Puede acceder al módulo enterprise
- ✅ Puede acceder a clientes (vista programador)
- ✅ Puede acceder al monitor de APIs
- ✅ Puede acceder a reglas de negocio
- ✅ NO puede acceder al admin dashboard (403)
- ✅ NO puede acceder a gestión de usuarios (403)

#### Operador (8 tests)
- ✅ Login va al dashboard operador
- ✅ Puede acceder a clientes
- ✅ Puede acceder al monitor de APIs
- ✅ Puede acceder a perfil
- ✅ Puede acceder a configuración
- ✅ NO puede acceder al admin dashboard (403)
- ✅ NO puede acceder a rutas programador (403)
- ✅ NO puede acceder a gestión de usuarios (403)

#### Guest - Sin autenticación (4 tests)
- ✅ Dashboard redirige a login
- ✅ Rutas admin redirigen a login
- ✅ Rutas programador redirigen a login
- ✅ Clientes redirige a login

#### Navegación (5 tests)
- ✅ Super Admin ve menú admin
- ✅ Programador ve menú programador
- ✅ Operador ve menú operador
- ✅ Operador NO ve menú admin
- ✅ Programador NO ve menú admin

---

## 📊 Matriz de Permisos

### Archivo: `tests/Feature/RoleMatrixTest.php`

| Permiso | Super Admin | Manager | Programador | Operador |
|---------|-------------|---------|-------------|----------|
| view clients | ✅ | ✅ | ✅ | ✅ |
| create clients | ✅ | ✅ | ✅ | ✅ |
| edit clients | ✅ | ✅ | ✅ | ✅ |
| delete clients | ✅ | ✅ | ❌ | ❌ |
| restore clients | ✅ | ✅ | ❌ | ❌ |
| reassign clients | ✅ | ✅ | ✅ | ❌ |
| view api catalog | ✅ | ✅ | ✅ | ✅ |
| manage api catalog | ✅ | ❌ | ❌ | ❌ |
| manage credentials | ✅ | ❌ | ✅ | ✅ |
| manage users | ✅ | ❌ | ❌ | ❌ |

---

## 🚀 Comandos de Ejecución

### Ejecutar todos los tests:
```bash
php artisan test
```

### Ejecutar solo tests de roles:
```bash
php artisan test --filter=LoginByRoleTest
```

### Ejecutar matriz de permisos:
```bash
php artisan test --filter=RoleMatrixTest
```

### Ejecutar tests de acceso:
```bash
php artisan test --filter=RoleAccessTest
```

### Ejecutar todos los tests Feature:
```bash
php artisan test tests/Feature
```

### Ejecutar con salida detallada:
```bash
php artisan test --filter=LoginByRoleTest -v
```

---

## ⚙️ Configuración de Testing

### Base de Datos
- **Motor**: SQLite
- **Archivo**: `database/testing.sqlite`
- **Trait**: `LazilyRefreshDatabase` (aplicado globalmente en Pest)

### Preparar antes de ejecutar:
```bash
# Crear archivo SQLite (si no existe)
New-Item -ItemType File -Force -Path "database/testing.sqlite"

# O en Linux/Mac:
touch database/testing.sqlite
```

### Archivos de configuración:
- `phpunit.xml` - Configuración PHPUnit
- `tests/Pest.php` - Configuración Pest global
- `tests/TestCase.php` - Clase base de tests

---

## 📁 Estructura de Tests

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── AuthenticationTest.php
│   │   ├── EmailVerificationTest.php
│   │   ├── PasswordConfirmationTest.php
│   │   ├── PasswordResetTest.php
│   │   ├── PasswordUpdateTest.php
│   │   └── RegistrationTest.php
│   ├── BranchIsolationTest.php
│   ├── CredentialFlowTest.php
│   ├── ExampleTest.php
│   ├── LoginByRoleTest.php      ← Tests de login por rol
│   ├── ProfileTest.php
│   ├── RoleAccessTest.php       ← Tests de acceso a rutas
│   └── RoleMatrixTest.php       ← Tests de matriz permisos
├── Unit/
│   └── ExampleTest.php
├── Pest.php
└── TestCase.php
```

---

## 📝 Ejemplo de Resultado

```
   PASS  Tests\Feature\LoginByRoleTest
  ✓ super admin login redirects to admin dashboard   0.94s
  ✓ super admin can access admin dashboard           0.15s
  ✓ super admin can access user management           0.12s
  ✓ super admin can access api services              0.11s
  ✓ super admin can access email settings            0.10s
  ✓ manager login redirects to admin dashboard       0.08s
  ✓ manager can access admin dashboard               0.07s
  ✓ manager can access user management               0.07s
  ✓ manager cannot access programmer routes          0.06s
  ✓ programador login redirects to programmer...     0.06s
  ... (24 más)

  Tests:    34 passed (57 assertions)
  Duration: 6.31s
```
