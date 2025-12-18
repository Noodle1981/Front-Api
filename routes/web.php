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
    // Redirigir a administradores a su panel
    if (auth()->check() && auth()->user()->hasRole('Super Admin')) {
        return redirect()->route('admin.dashboard');
    }
    return app(DashboardController::class)->index();
})->middleware(['auth', 'verified', 'user'])->name('dashboard');

Route::middleware(['auth', 'user', 'role:Super Admin|Manager|Analista|User'])->group(function () {
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

// --- RUTAS PARA ANALISTAS (Inspectores) ---
Route::middleware(['auth', 'role:Analista'])->prefix('analistas')->name('analyst.')->group(function () {
    // Dashboard Inspector
    Route::get('/dashboard', [App\Http\Controllers\AnalystDashboardController::class, 'index'])->name('dashboard');
    
    // API Monitor (Analyst View)
    Route::get('/api-dashboard', [App\Http\Controllers\Admin\ApiDashboardController::class, 'index'])->name('api-dashboard');

    // Clientes (Inspector View - Reusing Controller but strictly for Analysts)
    Route::resource('clients', ClientController::class);
    
    // Transferir Clientes
    Route::get('/clients/{client}/transfer', [App\Http\Controllers\ClientTransferController::class, 'edit'])->name('clients.transfer');
    Route::put('/clients/{client}/transfer', [App\Http\Controllers\ClientTransferController::class, 'update'])->name('clients.transfer.update');
});

Route::middleware(['auth', 'role:Super Admin|Manager'])->prefix('admin')->name('admin.')->group(function () {
    // Panel de control del administrador
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Gestión de usuarios
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);

    // Logs y mantenimiento
    Route::get('/system-logs', [App\Http\Controllers\Admin\DashboardController::class, 'logs'])->name('logs');
    Route::get('/maintenance', [App\Http\Controllers\Admin\MaintenanceController::class, 'index'])->name('maintenance');
    Route::post('/maintenance/backup', [App\Http\Controllers\Admin\MaintenanceController::class, 'backup'])->name('maintenance.backup');
    Route::post('/maintenance/optimize', [App\Http\Controllers\Admin\MaintenanceController::class, 'optimize'])->name('maintenance.optimize');
    Route::post('/maintenance/clear-cache', [App\Http\Controllers\Admin\MaintenanceController::class, 'clearCache'])->name('maintenance.clear-cache');
    Route::post('/maintenance/clear-views', [App\Http\Controllers\Admin\MaintenanceController::class, 'clearViews'])->name('maintenance.clear-views');
    Route::post('/maintenance/clean-logs', [App\Http\Controllers\Admin\MaintenanceController::class, 'cleanLogs'])->name('maintenance.clean-logs');
    Route::post('/maintenance/clean-sessions', [App\Http\Controllers\Admin\MaintenanceController::class, 'cleanSessions'])->name('maintenance.clean-sessions');
    
    // Email Settings
    Route::get('/email-settings', [App\Http\Controllers\Admin\EmailSettingsController::class, 'index'])->name('email.settings');
    Route::post('/email-settings/test', [App\Http\Controllers\Admin\EmailSettingsController::class, 'testEmail'])->name('email.test');
    Route::get('/email-history', [App\Http\Controllers\Admin\EmailSettingsController::class, 'history'])->name('email.history');
    Route::get('/email-stats', [App\Http\Controllers\Admin\EmailSettingsController::class, 'stats'])->name('email.stats');

    // Configuraciones del sistema
    Route::get('/settings/email', [App\Http\Controllers\Admin\SettingsController::class, 'emailConfig'])->name('settings.email');
    Route::post('/settings/email', [App\Http\Controllers\Admin\SettingsController::class, 'updateEmailConfig'])->name('settings.email.update');

    // Gestión de Servicios API
    Route::resource('api-services', App\Http\Controllers\Admin\ApiServiceController::class);
});

require __DIR__ . '/auth.php';