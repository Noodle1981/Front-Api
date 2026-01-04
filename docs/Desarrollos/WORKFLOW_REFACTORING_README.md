# Workflow System Refactoring - Setup Instructions

## 📋 Resumen de Cambios

Se ha refactorizado el sistema de workflows para integrar con una API de Python y generar reportes PDF multi-hoja.

### Cambios Principales:

1. **Wizard de 3 Pasos** (antes 4):
   - Paso 1: Cliente y Sucursal
   - Paso 2: Tipo de Workflow
   - Paso 3: Archivos + Ejecución (con barra de progreso)

2. **Barra de Progreso Visual** con 6 estados:
   - Analizando tipo de archivo (10%)
   - Analizando archivos (20%)
   - Analizando contenido (40%)
   - Ejecutando workflow (50%)
   - Esperando respuesta del servidor (70%)
   - Generando reporte (90%)

3. **Integración con API Python**:
   - Envío de archivos Excel a API Python
   - Recepción de Excel procesado
   - Modo MOCK para testing sin servidor activo

4. **Nueva Base de Datos**:
   - Campo `excel_response_path` en `workflow_executions`

---

## ⚙️ Configuración Requerida

### 1. Variables de Entorno

Agrega estas variables a tu archivo `.env`:

```env
# Workflow Python API Configuration
WORKFLOW_PYTHON_API_URL=http://localhost:8000/procesar
WORKFLOW_PYTHON_API_TIMEOUT=120
WORKFLOW_PYTHON_API_MOCK=true
```

> **Nota**: Mientras el servidor Python no esté activo, mantén `WORKFLOW_PYTHON_API_MOCK=true` para usar datos simulados.

### 2. Ejecutar Migración

La migración ya fue ejecutada, pero si necesitas revertir:

```bash
# Revertir
php artisan migrate:rollback --step=1

# Ejecutar nuevamente
php artisan migrate
```

---

## 🧪 Testing del Sistema

### Modo MOCK (Sin Servidor Python)

1. Asegúrate que `WORKFLOW_PYTHON_API_MOCK=true` en `.env`
2. Navega a: `http://127.0.0.1:8000/programadores/workflows/upload`
3. Completa los 3 pasos:
   - **Paso 1**: Selecciona un cliente y sucursal
   - **Paso 2**: Selecciona "Conciliación" como tipo de workflow
   - **Paso 3**: Carga los 6 archivos Excel requeridos:
     - Turnos.xlsx
     - Reporte_Ventas.xlsx
     - Reporte_getnet.xlsx
     - Prueba_MP.xlsx (Mercado Pago)
     - Devoluciones.xlsx
     - Caja_Adicion.xlsx
4. Haz clic en "EJECUTAR WORKFLOW"
5. Observa la barra de progreso con los 6 estados
6. El sistema generará un Excel simulado con 4 hojas:
   - ENVIAR SUCURSAL
   - ENVIAR EGRESOS
   - ENVIAR NO CONCILIADOS
   - ENVIAR ANULACIONES

### Verificar Resultado

1. Ve a: `http://127.0.0.1:8000/programadores/workflows/history`
2. Busca la ejecución más reciente
3. Verifica que el estado sea "success"
4. El Excel generado estará en: `storage/app/workflows/executions/{execution_id}/`

---

## 📁 Archivos Creados/Modificados

### Nuevos Archivos:
- `app/Services/WorkflowPythonApiService.php` - Servicio para API Python con modo mock
- `database/migrations/2026_01_04_161523_add_excel_response_path_to_workflow_executions_table.php`

### Archivos Modificados:
- `app/Livewire/WorkflowFileUploadWizard.php` - Refactorizado a 3 pasos
- `app/Models/WorkflowExecution.php` - Agregado campo `excel_response_path`
- `config/services.php` - Configuración de API Python
- `resources/views/livewire/workflow-file-upload-wizard.blade.php` - Vista de 3 pasos
- `resources/views/livewire/wizard-steps/step3-files.blade.php` - Barra de progreso

---

## 🔄 Próximos Pasos

### Pendientes de Implementación:

1. **PDF Service**: Actualizar `WorkflowPdfService.php` para leer Excel y generar PDF multi-hoja
2. **History Table**: Agregar botón "Ver PDF" en el historial
3. **Rutas**: Crear ruta para preview/download de PDF
4. **Testing**: Probar flujo completo con servidor Python real

### Cuando el Servidor Python esté Listo:

1. Cambia `WORKFLOW_PYTHON_API_MOCK=false` en `.env`
2. Actualiza `WORKFLOW_PYTHON_API_URL` con la URL real del servidor
3. Prueba el flujo completo con archivos reales

---

## 🐛 Troubleshooting

### El wizard no muestra 3 pasos
- Limpia la caché de Livewire: `php artisan livewire:clear`
- Recarga la página con Ctrl+F5

### La barra de progreso no se muestra
- Verifica que `$isProcessing` esté en `true` durante la ejecución
- Revisa los logs en `storage/logs/laravel.log`

### Error "excel_response_path column not found"
- Ejecuta la migración: `php artisan migrate`

---

## 📞 Soporte

Si encuentras algún problema, revisa:
1. Logs de Laravel: `storage/logs/laravel.log`
2. Consola del navegador (F12)
3. Estado de las migraciones: `php artisan migrate:status`
