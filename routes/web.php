<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Redirigir a administradores y managers a su panel
    if (auth()->user()->hasAnyRole(['Super Admin', 'Manager'])) {
        return redirect()->route('admin.dashboard');
    }
    
    // Redirigir programadores a su dashboard
    if (auth()->user()->hasRole('Programador')) {
        return redirect()->route('programmer.dashboard');
    }
    
    // Redirigir operadores a su dashboard
    if (auth()->user()->hasRole('Operador')) {
        return redirect()->route('operator.dashboard');
    }

    // Default legacy redirect
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Super Admin|Manager|Programador|Operador'])->group(function () {
    // Rutas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Configuración personal
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Rutas para Clientes
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/data', [ClientController::class, 'data'])->name('clients.data');

    // Rutas desactivar y reactivar clientes
    Route::post('/clients/{client}/deactivate', [ClientController::class, 'deactivate'])->name('clients.deactivate');
    Route::post('/clients/{client}/activate', [ClientController::class, 'activate'])->name('clients.activate');

    // Rutas Credenciales
    Route::post('/clients/{client}/credentials', [App\Http\Controllers\ClientCredentialController::class, 'store'])->name('clients.credentials.store');
    Route::put('/credentials/{credential}', [App\Http\Controllers\ClientCredentialController::class, 'update'])->name('credentials.update');
    Route::delete('/credentials/{credential}', [App\Http\Controllers\ClientCredentialController::class, 'destroy'])->name('credentials.destroy');

    // Dashboard de APIs (Monitor) - Accesible para todos los roles (Legacy/User)
    Route::get('/api-dashboard', [App\Http\Controllers\Admin\ApiDashboardController::class, 'index'])->name('api.dashboard');
});

// --- RUTAS PARA OPERADORES ---
Route::middleware(['auth', 'role:Operador'])->prefix('operador')->name('operator.')->group(function () {
    // Dashboard Operador
    Route::get('/dashboard', [App\Http\Controllers\Operator\DashboardController::class, 'index'])->name('dashboard');

    // Historial de Workflows (reutiliza el componente del programador pero filtrado por clientes del operador)
    Route::get('/workflows/history', App\Livewire\WorkflowHistoryTable::class)->name('workflows.history');
    
    // Mis Pedidos de Workflows
    Route::get('/workflows/my-requests', [App\Http\Controllers\Operator\WorkflowRequestController::class, 'index'])->name('workflows.my-requests');
    
    // Solicitar Workflow (nuevo feature)
    Route::get('/workflows/request', function () {
        return view('operator.workflows.request');
    })->name('workflows.request');
    Route::post('/workflows/request', [App\Http\Controllers\Operator\WorkflowRequestController::class, 'store'])->name('workflows.request.store');
    
    // PDF Routes (acceso a resultados de workflows)
    Route::get('/workflows/execution/{execution}/pdf/preview', [App\Http\Controllers\WorkflowPdfController::class, 'preview'])->name('workflows.execution.pdf.preview');
    Route::get('/workflows/execution/{execution}/pdf/download', [App\Http\Controllers\WorkflowPdfController::class, 'download'])->name('workflows.execution.pdf.download');
    
    // Estado de APIs (vista próximamente)
    Route::get('/api/status', function () {
        return view('operator.api-status');
    })->name('api.status');
});

// --- RUTAS PARA PROGRAMADORES (Ex Analistas) ---
Route::middleware(['auth', 'role:Programador'])->prefix('programadores')->name('programmer.')->group(function () {
    // Dashboard Programador
    Route::get('/dashboard', [App\Http\Controllers\ProgrammerDashboardController::class, 'index'])->name('dashboard');
    
    // API Monitor (Programmer View)
    Route::get('/api-dashboard', [App\Http\Controllers\Admin\ApiDashboardController::class, 'index'])->name('api-dashboard');

    // Endpoint Test execution
    Route::post('/endpoints/execute-test', [App\Http\Controllers\EndpointController::class, 'executeTest'])->name('endpoints.execute_test');

    // Enterprise Module (API Instances / Credentials)
    Route::resource('enterprise', App\Http\Controllers\EnterpriseController::class);

    // API Templates & Endpoints
    Route::prefix('services/{service}')->name('services.')->group(function() {
        Route::resource('endpoints', App\Http\Controllers\EndpointController::class);
    });

    // Clientes (Programmer View - Reusing Controller but strictly for Programmers)
    Route::resource('clients', ClientController::class);
    
    // Transferir Clientes
    Route::get('/clients/{client}/transfer', [App\Http\Controllers\ClientTransferController::class, 'edit'])->name('clients.transfer');
    Route::put('/clients/{client}/transfer', [App\Http\Controllers\ClientTransferController::class, 'update'])->name('clients.transfer.update');

    // Módulo de Integraciones (Wizard)
    Route::get('/integrations/new', [App\Http\Controllers\Programmer\IntegrationController::class, 'create'])->name('integrations.create');
    Route::post('/integrations/test', [App\Http\Controllers\Programmer\IntegrationController::class, 'testConnection'])->name('integrations.test'); // Add this line
    Route::get('/integrations/{provider}/configure', [App\Http\Controllers\Programmer\IntegrationController::class, 'configure'])->name('integrations.configure');
    Route::post('/integrations/{provider}', [App\Http\Controllers\Programmer\IntegrationController::class, 'store'])->name('integrations.store');

    // Reglas de Negocio ETL
    Route::prefix('reglas')->name('business-rules.')->group(function () {
        Route::get('/', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'store'])->name('store');
        Route::get('/{rule}/edit', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'edit'])->name('edit');
        Route::put('/{rule}', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'update'])->name('update');
        Route::delete('/{rule}', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'destroy'])->name('destroy');
        
        // API endpoints para AJAX
        Route::get('/api/services/{apiService}/endpoints', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'getEndpoints'])->name('api.endpoints');
        Route::get('/api/input-data', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'getInputData'])->name('api.input-data');
        Route::post('/{rule}/execute-test', [App\Http\Controllers\Programmer\BusinessRuleController::class, 'executeTest'])->name('execute-test');
    });

    // Workflows - Sistema de carga de archivos
    Route::prefix('workflows')->name('workflows.')->group(function () {
        Route::get('/upload', App\Livewire\WorkflowFileUploadWizard::class)->name('upload');
        Route::get('/batch/{batch}', [App\Http\Controllers\WorkflowBatchController::class, 'show'])->name('batch.show');
        Route::get('/history', App\Livewire\WorkflowHistoryTable::class)->name('history');
        Route::get('/test', [App\Http\Controllers\WorkflowBatchController::class, 'test'])->name('test');
        
        // PDF Routes
        Route::get('/execution/{execution}/pdf/preview', [App\Http\Controllers\WorkflowPdfController::class, 'preview'])->name('execution.pdf.preview');
        Route::get('/execution/{execution}/pdf/download', [App\Http\Controllers\WorkflowPdfController::class, 'download'])->name('execution.pdf.download');

        // Workflow Requests from Operators
        Route::get('/requests', [App\Http\Controllers\Programmer\WorkflowRequestController::class, 'index'])->name('requests');
        Route::post('/requests/{request}/accept', [App\Http\Controllers\Programmer\WorkflowRequestController::class, 'accept'])->name('requests.accept');
        Route::post('/requests/{request}/reject', [App\Http\Controllers\Programmer\WorkflowRequestController::class, 'reject'])->name('requests.reject');
        Route::get('/requests/{request}/execute', [App\Http\Controllers\Programmer\WorkflowRequestController::class, 'execute'])->name('requests.execute');
    });

    // Logs y Mantenimiento del Sistema (movido desde Admin)
    Route::get('/system-logs', [App\Http\Controllers\Admin\DashboardController::class, 'logs'])->name('logs');
    Route::get('/maintenance', [App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('maintenance');
    Route::post('/maintenance/backup', [App\Http\Controllers\Admin\MaintenanceController::class, 'backup'])->name('maintenance.backup');
    Route::post('/maintenance/optimize', [App\Http\Controllers\Admin\MaintenanceController::class, 'optimize'])->name('maintenance.optimize');
    Route::post('/maintenance/clear-cache', [App\Http\Controllers\Admin\MaintenanceController::class, 'clearCache'])->name('maintenance.clear-cache');
    Route::post('/maintenance/clear-views', [App\Http\Controllers\Admin\MaintenanceController::class, 'clearViews'])->name('maintenance.clear-views');
    Route::post('/maintenance/clean-logs', [App\Http\Controllers\Admin\MaintenanceController::class, 'cleanLogs'])->name('maintenance.clean-logs');
    Route::post('/maintenance/clean-sessions', [App\Http\Controllers\Admin\MaintenanceController::class, 'cleanSessions'])->name('maintenance.clean-sessions');
});

Route::middleware(['auth', 'role:Super Admin|Manager'])->prefix('admin')->name('admin.')->group(function () {
    // Panel de control del administrador
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Gestión de usuarios
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    // Gestión de Servicios API
    Route::resource('api-services', App\Http\Controllers\Admin\ApiServiceController::class);
});

require __DIR__ . '/auth.php';