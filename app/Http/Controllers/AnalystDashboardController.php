<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\ApiLog;
use App\Models\ClientCredential;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalystDashboardController extends Controller
{
    public function index()
    {
        // Global Stats
        $stats = [
            'total_users' => User::role('User')->count(),
            'total_clients' => Client::count(),
            'active_clients' => Client::where('active', true)->count(),
            'errors_today' => ApiLog::where('status', 'error')->whereDate('happened_at', now())->count(),
            'transactions_today' => Transaction::whereDate('created_at', now())->count(),
        ];

        // System Health Status
        $errorRate = $stats['errors_today'] > 0 && ApiLog::whereDate('happened_at', now())->count() > 0
            ? ($stats['errors_today'] / ApiLog::whereDate('happened_at', now())->count()) * 100
            : 0;
        
        $stats['system_health'] = $errorRate < 5 ? 'healthy' : ($errorRate < 15 ? 'warning' : 'critical');
        $stats['error_rate'] = round($errorRate, 1);

        // Trend vs Last Week
        $lastWeekErrors = ApiLog::where('status', 'error')
            ->whereBetween('happened_at', [now()->subWeeks(2), now()->subWeek()])
            ->count();
        $stats['trend'] = $stats['errors_today'] < $lastWeekErrors ? 'improving' : 'declining';

        // Users with Enhanced Metrics
        $users = User::role('User')
            ->withCount('clients')
            ->get()
            ->map(function($user) {
                // Client IDs for this user
                $clientIds = $user->clients->pluck('id');
                
                // Errors in last 7 days
                $errorsLast7Days = ApiLog::whereIn('client_id', $clientIds)
                    ->where('status', 'error')
                    ->where('happened_at', '>=', now()->subDays(7))
                    ->count();
                
                // Total API calls last 7 days
                $totalCallsLast7Days = ApiLog::whereIn('client_id', $clientIds)
                    ->where('happened_at', '>=', now()->subDays(7))
                    ->count();
                
                // Error rate
                $user->error_rate = $totalCallsLast7Days > 0 
                    ? round(($errorsLast7Days / $totalCallsLast7Days) * 100, 1) 
                    : 0;
                
                // Automation percentage
                $totalCredentials = ClientCredential::whereIn('client_id', $clientIds)
                    ->where('is_active', true)
                    ->count();
                $automatedCredentials = ClientCredential::whereIn('client_id', $clientIds)
                    ->where('is_active', true)
                    ->whereNotNull('execution_frequency')
                    ->count();
                
                $user->automation_percent = $totalCredentials > 0 
                    ? round(($automatedCredentials / $totalCredentials) * 100) 
                    : 0;
                
                // Last activity
                $lastLog = ApiLog::whereIn('client_id', $clientIds)
                    ->latest('happened_at')
                    ->first();
                $user->last_activity = $lastLog ? $lastLog->happened_at : null;
                $user->days_inactive = $lastLog ? now()->diffInDays($lastLog->happened_at) : 999;
                
                // Workload indicator (clients per user)
                $user->workload = $user->clients_count < 5 ? 'low' : ($user->clients_count < 15 ? 'medium' : 'high');
                
                // Alert flags
                $user->has_alerts = $user->error_rate > 10 || $user->days_inactive > 7;
                
                return $user;
            });

        // Alerts
        $alerts = [];
        
        // Critical: High error rate users
        $highErrorUsers = $users->filter(fn($u) => $u->error_rate > 10);
        if ($highErrorUsers->count() > 0) {
            $alerts[] = [
                'level' => 'critical',
                'icon' => 'fa-exclamation-triangle',
                'message' => $highErrorUsers->count() . ' contador(es) con tasa de error >10%',
                'users' => $highErrorUsers->pluck('name')->toArray()
            ];
        }
        
        // Warning: Inactive users
        $inactiveUsers = $users->filter(fn($u) => $u->days_inactive > 7);
        if ($inactiveUsers->count() > 0) {
            $alerts[] = [
                'level' => 'warning',
                'icon' => 'fa-clock',
                'message' => $inactiveUsers->count() . ' contador(es) sin actividad >7 días',
                'users' => $inactiveUsers->pluck('name')->toArray()
            ];
        }
        
        // Info: Low automation
        $lowAutoUsers = $users->filter(fn($u) => $u->automation_percent < 30 && $u->clients_count > 0);
        if ($lowAutoUsers->count() > 0) {
            $alerts[] = [
                'level' => 'info',
                'icon' => 'fa-robot',
                'message' => $lowAutoUsers->count() . ' contador(es) con baja automatización (<30%)',
                'users' => $lowAutoUsers->pluck('name')->toArray()
            ];
        }

        // Rankings
        $rankings = [
            'most_errors' => $users->sortByDesc('error_rate')->take(3)->values(),
            'most_efficient' => $users->filter(fn($u) => $u->clients_count > 0)
                ->sortBy('error_rate')
                ->take(3)
                ->values(),
            'most_automated' => $users->filter(fn($u) => $u->clients_count > 0)
                ->sortByDesc('automation_percent')
                ->take(3)
                ->values(),
        ];

        // Chart Data: Error Trend (Last 7 Days)
        $errorTrend = [
            'labels' => [],
            'data' => []
        ];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $errorTrend['labels'][] = $date->format('d/m');
            $errorTrend['data'][] = ApiLog::where('status', 'error')
                ->whereDate('happened_at', $date->format('Y-m-d'))
                ->count();
        }

        return view('analyst.dashboard', compact('stats', 'users', 'alerts', 'rankings', 'errorTrend'));
    }
}
