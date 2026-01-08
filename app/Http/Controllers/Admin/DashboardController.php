<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use App\Models\WorkflowExecution;
use App\Models\WorkflowType;
use App\Services\WorkflowMetricsService;
use Illuminate\Http\Request;

/**
 * Controlador para el panel de administración
 * Enfoque: ROI, Métricas de Negocio y Conciliación
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filters from request
        $filters = [
            'month' => $request->get('month', now()->month),
            'year' => $request->get('year', now()->year),
        ];

        $metricsService = new WorkflowMetricsService();
        $workflowMetrics = $metricsService->getDashboardMetrics($filters);

        // Estadísticas de Negocio
        $stats = [
            'totalClients' => Client::count(),
            'workflowTypes' => WorkflowType::where('is_active', true)->count(),
            'totalExecutions' => $workflowMetrics['total_executions'],
            'totalTickets' => $workflowMetrics['total_tickets'],
            'totalVentas' => $workflowMetrics['total_ventas'],
            'totalComensales' => $workflowMetrics['total_comensales'],
            'avgExecutionTime' => round($workflowMetrics['avg_execution_time_ms'] / 1000, 2),
            'successRate' => $workflowMetrics['total_executions'] > 0 
                ? round((($workflowMetrics['total_executions'] - WorkflowExecution::where('status', 'failed')
                    ->when($filters['month'], fn($q) => $q->whereMonth('created_at', $filters['month']))
                    ->when($filters['year'], fn($q) => $q->whereYear('created_at', $filters['year']))
                    ->count()) / $workflowMetrics['total_executions']) * 100)
                : 100,
            
            // Benefit metrics
            'hours_saved' => $workflowMetrics['hours_saved'] ?? 0,
            'cost_saved' => $workflowMetrics['cost_saved'] ?? 0,
            'efficiency_gain' => $workflowMetrics['efficiency_gain'] ?? 0,
        ];

        // Panel de Conciliación
        $conciliacion = [
            'mercado_pago' => $workflowMetrics['avg_conciliacion_mp'],
            'getnet' => $workflowMetrics['avg_conciliacion_getnet'],
            'efectivo' => $workflowMetrics['avg_conciliacion_efectivo'],
            'promedio' => round(
                ($workflowMetrics['avg_conciliacion_mp'] + 
                 $workflowMetrics['avg_conciliacion_getnet'] + 
                 $workflowMetrics['avg_conciliacion_efectivo']) / 3, 
                2
            ),
        ];

        // Workflows por programador
        $workflowsByProgrammer = User::role('Programador')
            ->withCount(['workflowBatches as workflows_count' => function($query) use ($filters) {
                if (!empty($filters['month'])) {
                    $query->whereMonth('created_at', $filters['month']);
                }
                if (!empty($filters['year'])) {
                    $query->whereYear('created_at', $filters['year']);
                }
            }])
            ->orderByDesc('workflows_count')
            ->take(10)
            ->get()
            ->filter(fn($user) => $user->workflows_count > 0);

        // Últimas ejecuciones para actividad reciente
        $recentExecutions = WorkflowExecution::with(['fileBatch.client', 'fileBatch.user', 'fileBatch.workflowType'])
            ->whereIn('status', ['completed', 'success'])
            ->when($filters['month'], fn($q) => $q->whereMonth('completed_at', $filters['month']))
            ->when($filters['year'], fn($q) => $q->whereYear('completed_at', $filters['year']))
            ->latest('completed_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'conciliacion', 'workflowsByProgrammer', 'recentExecutions', 'workflowMetrics', 'filters'));
    }

    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (file_exists($logPath)) {
            $file = file($logPath);
            $lines = array_slice($file, -500);
            $lines = array_reverse($lines);

            foreach ($lines as $line) {
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

        return view('admin.logs', compact('logs'));
    }
}