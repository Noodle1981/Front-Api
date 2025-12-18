<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;

/**
 * Controlador para el panel de administración
 */

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'activeUsers' => User::where('is_active', true)->count(),
            'totalClients' => Client::count(),
            'totalApis' => \App\Models\ApiService::count(),
            // 'activeScripts' => \App\Models\ClientCredential::whereNotNull('execution_time')->count(), // Future Stage 8
            'activeScripts' => 0, // Placeholder
        ];

        // Distribución de clientes por usuario
        $usersWithClients = User::where('is_active', true)
            ->withCount('clients')
            ->orderByDesc('clients_count')
            ->take(10) // Top 10 para no saturar
            ->get();

        return view('admin.dashboard', compact('stats', 'usersWithClients'));
    }

    // public function stats() ... removed
    // private function getActivityColor/Icon ... removed

    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logPath)) {
            // Leer las últimas 500 líneas para no saturar memoria
            $file = file($logPath);
            $lines = array_slice($file, -500);
            $lines = array_reverse($lines); // Ver lo más nuevo primero

            foreach ($lines as $line) {
                // Regex simple para detectar fecha y nivel: [2024-01-01 10:00:00] local.ERROR:
                if (preg_match('/^\[(?<date>.*)\] (?<env>\w+)\.(?<level>\w+): (?<message>.*)/', $line, $matches)) {
                    $logs[] = [
                        'date' => $matches['date'],
                        'env' => $matches['env'],
                        'level' => $matches['level'],
                        'message' => substr($matches['message'], 0, 200) . (strlen($matches['message']) > 200 ? '...' : ''),
                        'raw' => $line
                    ];
                }
            }
        }

        // Paginación manual simple (array -> Paginator) se podría hacer, 
        // pero para simplificar pasaremos el array directo.
        // Si queremos paginar: new LengthAwarePaginator(...)

        return view('admin.logs', compact('logs'));
    }
}