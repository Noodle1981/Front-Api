@echo off
REM ============================================
REM Script para ejecutar tests de Front-Api
REM ============================================

echo.
echo ========================================
echo    FRONT-API TEST RUNNER
echo ========================================
echo.

REM Verificar que existe el archivo de testing.sqlite
if not exist "database\testing.sqlite" (
    echo [INFO] Creando base de datos de testing...
    copy nul database\testing.sqlite >nul
)

REM Opciones de ejecución
if "%1"=="" goto all
if "%1"=="roles" goto roles
if "%1"=="login" goto login
if "%1"=="matrix" goto matrix
if "%1"=="access" goto access
if "%1"=="auth" goto auth
if "%1"=="help" goto help

:all
echo [EJECUTANDO] Todos los tests Feature...
echo.
php artisan test tests/Feature
goto end

:roles
echo [EJECUTANDO] Tests de Roles (Login + Matrix + Access)...
echo.
php artisan test --filter="LoginByRoleTest|RoleMatrixTest|RoleAccessTest"
goto end

:login
echo [EJECUTANDO] Tests de Login por Rol...
echo.
php artisan test --filter=LoginByRoleTest
goto end

:matrix
echo [EJECUTANDO] Tests de Matriz de Permisos...
echo.
php artisan test --filter=RoleMatrixTest
goto end

:access
echo [EJECUTANDO] Tests de Acceso a Rutas...
echo.
php artisan test --filter=RoleAccessTest
goto end

:auth
echo [EJECUTANDO] Tests de Autenticación...
echo.
php artisan test tests/Feature/Auth
goto end

:help
echo.
echo USO: run-tests.bat [opcion]
echo.
echo Opciones disponibles:
echo   (sin opcion)  - Ejecuta todos los tests Feature
echo   roles         - Tests de Login, Matrix y Access
echo   login         - Solo LoginByRoleTest
echo   matrix        - Solo RoleMatrixTest  
echo   access        - Solo RoleAccessTest
echo   auth          - Tests de autenticación
echo   help          - Muestra esta ayuda
echo.
goto end

:end
echo.
echo ========================================
echo    EJECUCIÓN COMPLETADA
echo ========================================
