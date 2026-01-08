<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\WorkflowExecution;
use App\Models\WorkflowType;
use App\Services\WorkflowMetricsService;
use Illuminate\Http\Request;

/**
 * Controlador para el panel del programador
 * Enfoque: Operaciones Técnicas, Clientes Asignados y Salud del Sistema
 */
class ProgrammerDashboardController extends Controller
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

        // Estadísticas Operativas
        $stats = [
            'totalClients' => Client::count(), // Todos los clientes para consulta
            'totalExecutions' => $workflowMetrics['total_executions'],
            'avgExecutionTime' => round($workflowMetrics['avg_execution_time_ms'] / 1000, 2),
            'successRate' => $workflowMetrics['total_executions'] > 0 
                ? round((($workflowMetrics['total_executions'] - WorkflowExecution::where('status', 'failed')
                    ->whereMonth('created_at', $filters['month'])
                    ->whereYear('created_at', $filters['year'])
                    ->count()) / $workflowMetrics['total_executions']) * 100)
                : 100,
        ];

        // Stats de hoy (independientes del filtro de mes/año histórico para ver tiempo real)
        $stats['successToday'] = WorkflowExecution::whereIn('status', ['completed', 'success'])
            ->whereDate('completed_at', today())
            ->count();
        $stats['errorsToday'] = WorkflowExecution::where('status', 'failed')
            ->whereDate('created_at', today())
            ->count();

        // Mis Clientes Recientes (o todos si no tiene específicos)
        $recentClients = Client::latest()->take(5)->get();

        // Últimas Ejecuciones filtradas
        $recentExecutions = WorkflowExecution::with(['fileBatch.client', 'fileBatch.workflowType'])
            ->whereIn('status', ['completed', 'success'])
            ->whereMonth('completed_at', $filters['month'])
            ->whereYear('completed_at', $filters['year'])
            ->latest('completed_at')
            ->take(5)
            ->get();

        return view('programmer.dashboard', compact('stats', 'recentClients', 'recentExecutions', 'filters'));
    }
}
