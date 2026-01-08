<?php

namespace App\Services;

use App\Models\WorkflowExecution;
use Illuminate\Support\Collection;

/**
 * Service to calculate metrics from workflow executions
 * Uses json_response data stored in workflow_executions table
 */
class WorkflowMetricsService
{
    /**
     * Get summary metrics for the dashboard
     */
    public function getDashboardMetrics(): array
    {
        $executions = WorkflowExecution::where('status', 'completed')
            ->whereNotNull('json_response')
            ->get();

        if ($executions->isEmpty()) {
            return $this->getEmptyMetrics();
        }

        return [
            'total_executions' => $executions->count(),
            'total_tickets' => $this->sumFromExecutions($executions, 'enviar_sucursal.parador.cantidad_tickets'),
            'total_ventas' => $this->sumMoneyFromExecutions($executions, 'enviar_sucursal.total_ventas'),
            'total_comensales' => $this->sumFromExecutions($executions, 'enviar_sucursal.parador.cantidad_comensales'),
            'avg_execution_time_ms' => round($executions->avg('execution_time_ms') ?? 0),
            'avg_conciliacion_mp' => $this->avgFromExecutions($executions, 'enviar_sucursal.diferencias_caja.mercado_pago.porcentaje_conciliacion'),
            'avg_conciliacion_getnet' => $this->avgFromExecutions($executions, 'enviar_sucursal.diferencias_caja.getnet.porcentaje_conciliacion'),
            'avg_conciliacion_efectivo' => $this->avgFromExecutions($executions, 'enviar_sucursal.diferencias_caja.efectivo.porcentaje_conciliacion'),
            'last_execution' => $executions->sortByDesc('completed_at')->first(),
        ];
    }

    /**
     * Get metrics for a specific time period
     */
    public function getMetricsByPeriod(string $period = 'month'): array
    {
        $query = WorkflowExecution::where('status', 'completed')
            ->whereNotNull('json_response');

        switch ($period) {
            case 'today':
                $query->whereDate('completed_at', today());
                break;
            case 'week':
                $query->where('completed_at', '>=', now()->startOfWeek());
                break;
            case 'month':
                $query->whereMonth('completed_at', now()->month)
                      ->whereYear('completed_at', now()->year);
                break;
        }

        $executions = $query->get();

        return [
            'period' => $period,
            'count' => $executions->count(),
            'total_ventas' => $this->sumMoneyFromExecutions($executions, 'enviar_sucursal.total_ventas'),
            'avg_conciliacion' => $this->getAverageConciliacion($executions),
        ];
    }

    /**
     * Get the average conciliation percentage across all payment methods
     */
    private function getAverageConciliacion(Collection $executions): float
    {
        if ($executions->isEmpty()) return 0;

        $mp = $this->avgFromExecutions($executions, 'enviar_sucursal.diferencias_caja.mercado_pago.porcentaje_conciliacion');
        $getnet = $this->avgFromExecutions($executions, 'enviar_sucursal.diferencias_caja.getnet.porcentaje_conciliacion');
        $efectivo = $this->avgFromExecutions($executions, 'enviar_sucursal.diferencias_caja.efectivo.porcentaje_conciliacion');

        $count = 0;
        $sum = 0;
        
        if ($mp > 0) { $sum += $mp; $count++; }
        if ($getnet > 0) { $sum += $getnet; $count++; }
        if ($efectivo > 0) { $sum += $efectivo; $count++; }

        return $count > 0 ? round($sum / $count, 2) : 0;
    }

    /**
     * Extract value from nested array using dot notation
     */
    private function getNestedValue(array $data, string $path)
    {
        $keys = explode('.', $path);
        $value = $data;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                return null;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Sum numeric values from all executions
     */
    private function sumFromExecutions(Collection $executions, string $path): int
    {
        return $executions->sum(function ($execution) use ($path) {
            $value = $this->getNestedValue($execution->json_response ?? [], $path);
            return is_numeric($value) ? (int) $value : 0;
        });
    }

    /**
     * Sum money values (formatted strings like "1,234,567.00")
     */
    private function sumMoneyFromExecutions(Collection $executions, string $path): float
    {
        return $executions->sum(function ($execution) use ($path) {
            $value = $this->getNestedValue($execution->json_response ?? [], $path);
            if (!$value) return 0;
            // Remove commas and convert to float
            return (float) str_replace(',', '', $value);
        });
    }

    /**
     * Average numeric values from all executions
     */
    private function avgFromExecutions(Collection $executions, string $path): float
    {
        $values = $executions->map(function ($execution) use ($path) {
            $value = $this->getNestedValue($execution->json_response ?? [], $path);
            return is_numeric($value) ? (float) $value : null;
        })->filter();

        return $values->isNotEmpty() ? round($values->avg(), 2) : 0;
    }

    /**
     * Return empty metrics structure
     */
    private function getEmptyMetrics(): array
    {
        return [
            'total_executions' => 0,
            'total_tickets' => 0,
            'total_ventas' => 0,
            'total_comensales' => 0,
            'avg_execution_time_ms' => 0,
            'avg_conciliacion_mp' => 0,
            'avg_conciliacion_getnet' => 0,
            'avg_conciliacion_efectivo' => 0,
            'last_execution' => null,
        ];
    }

    /**
     * Format money for display
     */
    public static function formatMoney(float $value): string
    {
        return number_format($value, 2, ',', '.');
    }
}
