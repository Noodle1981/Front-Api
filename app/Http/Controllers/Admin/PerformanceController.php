<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Deal;
use App\Models\Client;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    /**
     * Mostrar métricas de rendimiento global
     */
    public function index()
    {
        // Métricas de conversión
        $conversionMetrics = $this->getConversionMetrics();
        
        // Rendimiento por usuario
        $userPerformance = $this->getUserPerformance();
        
        // Métricas de ventas
        $salesMetrics = $this->getSalesMetrics();
        
        // Métricas de actividad
        $activityMetrics = $this->getActivityMetrics();

        return view('admin.performance.index', compact(
            'conversionMetrics',
            'userPerformance',
            'salesMetrics',
            'activityMetrics'
        ));
    }

    /**
     * Obtener métricas de conversión
     */
    private function getConversionMetrics()
    {
        $totalLeads = Lead::count();
        $convertedLeads = Lead::where('status', 'convertido')->count();
        $totalDeals = Deal::count();
        $wonDeals = Deal::where('status', 'won')->count();

        return [
            'lead_conversion_rate' => $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0,
            'deal_win_rate' => $totalDeals > 0 ? ($wonDeals / $totalDeals) * 100 : 0,
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'total_deals' => $totalDeals,
            'won_deals' => $wonDeals,
        ];
    }

    /**
     * Obtener rendimiento por usuario
     */
    private function getUserPerformance()
    {
        return User::withCount(['clients', 'deals', 'leads'])
            ->withSum('deals as total_value', 'value')
            ->withCount(['deals as won_deals' => function($query) {
                $query->where('status', 'won');
            }])
            ->get()
            ->map(function($user) {
                $user->conversion_rate = $user->deals_count > 0 
                    ? ($user->won_deals / $user->deals_count) * 100 
                    : 0;
                return $user;
            });
    }

    /**
     * Obtener métricas de ventas
     */
    private function getSalesMetrics()
    {
        // Detectar el driver de base de datos
        $driver = DB::connection()->getDriverName();
        
        // Usar la función de formato de fecha apropiada según el driver
        if ($driver === 'sqlite') {
            $dateFormat = "strftime('%Y-%m', created_at)";
        } elseif ($driver === 'pgsql') {
            $dateFormat = "TO_CHAR(created_at, 'YYYY-MM')";
        } else {
            // MySQL/MariaDB
            $dateFormat = "DATE_FORMAT(created_at, '%Y-%m')";
        }

        return [
            'monthly_sales' => Deal::select(
                DB::raw("{$dateFormat} as month"),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(value) as value'),
                DB::raw("COUNT(CASE WHEN status = 'won' THEN 1 END) as won")
            )
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
                
            'pipeline_value' => Deal::where('status', 'open')
                ->sum('value'),
                
            'average_deal_size' => Deal::where('status', 'won')
                ->avg('value')
        ];
    }

    /**
     * Obtener métricas de actividad
     */
    private function getActivityMetrics()
    {
        // Verificar si la tabla activity_log existe
        $hasActivityLog = DB::getSchemaBuilder()->hasTable('activity_log');
        
        if (!$hasActivityLog) {
            // Si no existe, intentar con la tabla 'activities'
            $hasActivities = DB::getSchemaBuilder()->hasTable('activities');
            
            if ($hasActivities) {
                return [
                    'recent_activities' => DB::table('activities')
                        ->latest('created_at')
                        ->limit(50)
                        ->get(),
                        
                    'activity_by_type' => DB::table('activities')
                        ->select('type', DB::raw('COUNT(*) as total'))
                        ->groupBy('type')
                        ->orderByDesc('total')
                        ->limit(10)
                        ->get()
                ];
            }
            
            // Si ninguna tabla existe, retornar vacío
            return [
                'recent_activities' => collect(),
                'activity_by_type' => collect()
            ];
        }
        
        return [
            'recent_activities' => DB::table('activity_log')
                ->latest('created_at')
                ->limit(50)
                ->get(),
                
            'activity_by_type' => DB::table('activity_log')
                ->select('description', DB::raw('COUNT(*) as total'))
                ->groupBy('description')
                ->orderByDesc('total')
                ->limit(10)
                ->get()
        ];
    }

    /**
     * Exportar métricas en formato CSV
     */
    public function export()
    {
        // Obtener los datos
        $conversionMetrics = $this->getConversionMetrics();
        $userPerformance = $this->getUserPerformance();
        $salesMetrics = $this->getSalesMetrics();
        
        // Nombre del archivo
        $filename = 'metricas_rendimiento_' . date('Y-m-d_His') . '.csv';
        
        // Headers para descarga
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($conversionMetrics, $userPerformance, $salesMetrics) {
            $file = fopen('php://output', 'w');
            
            // BOM para UTF-8 (ayuda con caracteres especiales en Excel)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // === MÉTRICAS DE CONVERSIÓN ===
            fputcsv($file, ['MÉTRICAS DE CONVERSIÓN']);
            fputcsv($file, ['Métrica', 'Valor']);
            fputcsv($file, ['Total de Leads', $conversionMetrics['total_leads']]);
            fputcsv($file, ['Leads Convertidos', $conversionMetrics['converted_leads']]);
            fputcsv($file, ['Tasa de Conversión de Leads (%)', number_format($conversionMetrics['lead_conversion_rate'], 2)]);
            fputcsv($file, ['Total de Deals', $conversionMetrics['total_deals']]);
            fputcsv($file, ['Deals Ganados', $conversionMetrics['won_deals']]);
            fputcsv($file, ['Tasa de Cierre de Deals (%)', number_format($conversionMetrics['deal_win_rate'], 2)]);
            fputcsv($file, []); // Línea vacía
            
            // === RENDIMIENTO POR USUARIO ===
            fputcsv($file, ['RENDIMIENTO POR USUARIO']);
            fputcsv($file, ['Usuario', 'Email', 'Clientes', 'Deals', 'Deals Ganados', 'Valor Total', 'Tasa de Conversión (%)']);
            
            foreach($userPerformance as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->clients_count ?? 0,
                    $user->deals_count ?? 0,
                    $user->won_deals ?? 0,
                    '$' . number_format($user->total_value ?? 0, 2),
                    number_format($user->conversion_rate ?? 0, 2)
                ]);
            }
            fputcsv($file, []); // Línea vacía
            
            // === MÉTRICAS DE VENTAS ===
            fputcsv($file, ['MÉTRICAS DE VENTAS']);
            fputcsv($file, ['Valor en Pipeline', '$' . number_format($salesMetrics['pipeline_value'] ?? 0, 2)]);
            fputcsv($file, ['Tamaño Promedio de Deal', '$' . number_format($salesMetrics['average_deal_size'] ?? 0, 2)]);
            fputcsv($file, []); // Línea vacía
            
            // === VENTAS MENSUALES ===
            fputcsv($file, ['VENTAS MENSUALES']);
            fputcsv($file, ['Mes', 'Total Deals', 'Deals Ganados', 'Valor Total']);
            
            foreach($salesMetrics['monthly_sales'] as $month) {
                fputcsv($file, [
                    $month->month,
                    $month->total,
                    $month->won,
                    '$' . number_format($month->value ?? 0, 2)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}