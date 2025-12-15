<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Deal;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatsController extends Controller
{
    /**
     * Muestra estadísticas limitadas al usuario actual
     */
    public function index()
    {
        $user = auth()->user();
        
        // Estadísticas básicas
        $stats = [
            'clients_count' => Client::forCurrentUser()->count(),
            'active_deals' => Deal::forCurrentUser()
                ->whereNull('closed_at')
                ->whereNull('lost_at')
                ->count(),
            'total_value' => Deal::forCurrentUser()
                ->whereNotNull('closed_at')
                ->sum('value'),
            'leads_count' => Lead::forCurrentUser()->count(),
        ];

        // Rendimiento de ventas mensual
        $monthlyPerformance = Deal::forCurrentUser()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_deals'),
                DB::raw('SUM(CASE WHEN closed_at IS NOT NULL THEN value ELSE 0 END) as closed_value'),
                DB::raw('COUNT(CASE WHEN closed_at IS NOT NULL THEN 1 END) as won_deals')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        // Conversión de leads
        $leadStats = Lead::forCurrentUser()
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN converted THEN 1 END) as converted')
            )
            ->first();

        // Actividad reciente
        $recentActivity = DB::table('activity_log')
            ->where('causer_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('stats.user', compact('stats', 'monthlyPerformance', 'leadStats', 'recentActivity'));
    }

    /**
     * Muestra el pipeline personal del usuario
     */
    public function pipeline()
    {
        $pipeline = Deal::forCurrentUser()
            ->whereNull('closed_at')
            ->whereNull('lost_at')
            ->with('client')
            ->get()
            ->groupBy('stage');

        $pipelineValue = Deal::forCurrentUser()
            ->whereNull('closed_at')
            ->whereNull('lost_at')
            ->sum('value');

        return view('stats.pipeline', compact('pipeline', 'pipelineValue'));
    }

    /**
     * Muestra el rendimiento del mes actual
     */
    public function currentMonth()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $monthlyStats = [
            'new_clients' => Client::forCurrentUser()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'won_deals' => Deal::forCurrentUser()
                ->whereBetween('closed_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'total_value' => Deal::forCurrentUser()
                ->whereBetween('closed_at', [$startOfMonth, $endOfMonth])
                ->sum('value'),
            'converted_leads' => Lead::forCurrentUser()
                ->where('converted', true)
                ->whereBetween('converted_at', [$startOfMonth, $endOfMonth])
                ->count(),
        ];

        return view('stats.month', compact('monthlyStats'));
    }
}