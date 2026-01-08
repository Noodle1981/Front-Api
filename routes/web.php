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

    // Default legacy redirect
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:Super Admin|Manager|Programador'])->group(function () {
    // Rutas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    // Rutas para Clientes
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/data', [ClientController::class, 'data'])->name('clients.data');

    // Rutas desactivar y reactivar clientes
    Route::post('/clients/{client}/deactivate', [ClientController::class, 'deactivate'])->name('clients.deactivate');
    Route::post('/clients/{client}/activate', [ClientController::class, 'activate'])->name('clients.activate');


});

// --- RUTAS PARA PROGRAMADORES (Ex Analistas) ---
Route::middleware(['auth', 'role:Programador'])->prefix('programadores')->name('programmer.')->group(function () {
    // Dashboard Programador
    Route::get('/dashboard', [App\Http\Controllers\ProgrammerDashboardController::class, 'index'])->name('dashboard');

    // Clientes (Programmer View - Reusing Controller but strictly for Programmers)
    Route::resource('clients', ClientController::class);
    
    // Transferir Clientes
    Route::get('/clients/{client}/transfer', [App\Http\Controllers\ClientTransferController::class, 'edit'])->name('clients.transfer');
    Route::put('/clients/{client}/transfer', [App\Http\Controllers\ClientTransferController::class, 'update'])->name('clients.transfer.update');

    // Workflows - Sistema de carga de archivos
    Route::prefix('workflows')->name('workflows.')->group(function () {
        Route::get('/upload', App\Livewire\WorkflowFileUploadWizard::class)->name('upload');
        Route::get('/batch/{batch}', [App\Http\Controllers\WorkflowBatchController::class, 'show'])->name('batch.show');
        Route::get('/history', App\Livewire\WorkflowHistoryTable::class)->name('history');
        
        // PDF Routes
        Route::get('/execution/{execution}/pdf/preview', [App\Http\Controllers\WorkflowPdfController::class, 'preview'])->name('execution.pdf.preview');
        Route::get('/execution/{execution}/pdf/download', [App\Http\Controllers\WorkflowPdfController::class, 'download'])->name('execution.pdf.download');
    });
});

Route::middleware(['auth', 'role:Super Admin|Manager'])->prefix('admin')->name('admin.')->group(function () {
    // Panel de control del administrador
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Gestión de usuarios
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
});

require __DIR__ . '/auth.php';