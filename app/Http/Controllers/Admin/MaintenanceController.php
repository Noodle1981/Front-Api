<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MaintenanceController extends Controller
{
    /**
     * Muestra la página de mantenimiento
     */
    public function index()
    {
        $backups = Storage::disk('local')->files('backups');
        $latestBackup = end($backups) ?: null;
        
        return view('admin.maintenance', compact('backups', 'latestBackup'));
    }

    /**
     * Realiza un backup de la base de datos
     */
    public function backup()
    {
        try {
            // Nombre del archivo de backup
            $filename = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
            
            // Ejecutar el comando de backup
            Artisan::call('backup:run');
            
            return back()->with('success', '¡Backup creado correctamente!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el backup: ' . $e->getMessage());
        }
    }

    /**
     * Optimiza la aplicación
     */
    public function optimize()
    {
        try {
            Artisan::call('optimize');
            return back()->with('success', 'Aplicación optimizada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al optimizar: ' . $e->getMessage());
        }
    }

    /**
     * Limpia el caché
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            return back()->with('success', 'Caché limpiado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar caché: ' . $e->getMessage());
        }
    }

    /**
     * Limpia las vistas compiladas
     */
    public function clearViews()
    {
        try {
            Artisan::call('view:clear');
            return back()->with('success', 'Vistas compiladas limpiadas exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar vistas: ' . $e->getMessage());
        }
    }

    /**
     * Limpia logs antiguos (más de 7 días)
     */
    public function cleanLogs()
    {
        try {
            $logPath = storage_path('logs');
            $files = glob($logPath . '/*.log');
            
            // Mantener los últimos 7 días
            $keepDate = now()->subDays(7);
            $deleted = 0;
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileDate = Carbon::createFromTimestamp(filemtime($file));
                    if ($fileDate->lt($keepDate)) {
                        unlink($file);
                        $deleted++;
                    }
                }
            }
            
            return back()->with('success', "Se eliminaron {$deleted} archivos de log antiguos.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar logs: ' . $e->getMessage());
        }
    }

    /**
     * Limpia sesiones expiradas
     */
    public function cleanSessions()
    {
        try {
            // Limpiar sesiones de archivos si se usa el driver 'file'
            if (config('session.driver') === 'file') {
                $sessionPath = storage_path('framework/sessions');
                $files = glob($sessionPath . '/*');
                $deleted = 0;
                
                foreach ($files as $file) {
                    if (is_file($file)) {
                        // Si el archivo tiene más de 2 horas, eliminarlo
                        if (filemtime($file) < time() - 7200) {
                            unlink($file);
                            $deleted++;
                        }
                    }
                }
                
                return back()->with('success', "Se eliminaron {$deleted} sesiones expiradas.");
            } else {
                // Para database driver
                \DB::table(config('session.table', 'sessions'))
                    ->where('last_activity', '<', time() - 7200)
                    ->delete();
                    
                return back()->with('success', 'Sesiones expiradas limpiadas exitosamente.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar sesiones: ' . $e->getMessage());
        }
    }
}