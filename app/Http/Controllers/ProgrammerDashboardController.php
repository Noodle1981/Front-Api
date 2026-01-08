<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\WorkflowFileBatch;
use App\Models\WorkflowExecution;
use App\Services\WorkflowMetricsService;
use Carbon\Carbon;

class ProgrammerDashboardController extends Controller
{
    public function index()
    {
        $metricsService = new WorkflowMetricsService();
        
        // Get workflow metrics from json_response data
        $workflowMetrics = $metricsService->getDashboardMetrics();

        // Global Stats
        $stats = [
            'total_clients' => Client::count(),
            'total_executions' => $workflowMetrics['total_executions'],
            'total_tickets' => $workflowMetrics['total_tickets'],
            'total_ventas' => $workflowMetrics['total_ventas'],
            'total_comensales' => $workflowMetrics['total_comensales'],
            'avg_execution_time' => round($workflowMetrics['avg_execution_time_ms'] / 1000, 2), // Convert to seconds
        ];

        // Conciliation percentages
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

        // System Health Status (Based on Workflow Executions)
        $totalExecutions = WorkflowExecution::count();
        $failedExecutions = WorkflowExecution::where('status', 'failed')->count();
        
        $errorRate = $totalExecutions > 0 ? ($failedExecutions / $totalExecutions) * 100 : 0;
        
        $stats['system_health'] = $errorRate < 5 ? 'healthy' : ($errorRate < 15 ? 'warning' : 'critical');
        $stats['error_rate'] = round($errorRate, 1);
        $stats['success_rate'] = round(100 - $errorRate, 1);

        // Recent Executions
        $recentExecutions = WorkflowExecution::with(['fileBatch.client', 'fileBatch.workflowType'])
            ->where('status', 'completed')
            ->latest('completed_at')
            ->take(5)
            ->get();

        return view('programmer.dashboard', compact(
            'stats', 
            'conciliacion',
            'recentExecutions',
            'workflowMetrics'
        ));
    }
}
