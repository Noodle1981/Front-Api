<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiLog;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Definir si tiene acceso global
        $isGlobal = $user->hasRole(['Super Admin', 'Manager', 'Analista']);

        // User Context Filter (for Analysts to drill-down into specific users)
        $userFilter = $request->input('user_filter');
        $selectedUser = null;
        if ($userFilter && $isGlobal) {
            $selectedUser = \App\Models\User::find($userFilter);
        }

        // ---- Query Builders Base ----
        
        // Logs: Si no es global, filtrar por clientes asignados al usuario
        $logQuery = ApiLog::query();
        if (!$isGlobal) {
            $logQuery->whereHas('client', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($selectedUser) {
            // Analyst viewing specific user's data
            $logQuery->whereHas('client', function($q) use ($selectedUser) {
                $q->where('user_id', $selectedUser->id);
            });
        }

        // Clientes: Si no es global, filtrar por asignación directa
        $clientQuery = Client::query();
        if (!$isGlobal) {
            $clientQuery->where('user_id', $user->id);
        } elseif ($selectedUser) {
            // Analyst viewing specific user's clients
            $clientQuery->where('user_id', $selectedUser->id);
        }

        // 1. Stats for Cards
        $stats = [
            'errors_today' => (clone $logQuery)->whereDate('happened_at', Carbon::today())
                                    ->where('status', 'error')
                                    ->count(),
            'syncs_today'  => (clone $logQuery)->whereDate('happened_at', Carbon::today())
                                    ->where('status', 'success')
                                    ->count(),
            'connected_clients' => (clone $clientQuery)->whereHas('credentials', function($q) {
                                        $q->where('is_active', true);
                                    })->count(),
        ];

        // Automation stats (filter by user context if selected)
        $credentialQuery = \App\Models\ClientCredential::where('is_active', true);
        if ($selectedUser) {
            $credentialQuery->whereHas('client', function($q) use ($selectedUser) {
                $q->where('user_id', $selectedUser->id);
            });
        } elseif (!$isGlobal) {
            $credentialQuery->whereHas('client', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $stats['automated_apis'] = (clone $credentialQuery)->whereNotNull('execution_frequency')->count();
        $stats['manual_apis'] = (clone $credentialQuery)->whereNull('execution_frequency')->count();
        $stats['total_apis'] = (clone $credentialQuery)->count();

        // 3. Chart Data
        $range = $request->get('range', '7d');
        $endDate = Carbon::today();
        
        switch($range) {
            case '30d':
                $startDate = Carbon::today()->subDays(29);
                $groupBy = 'day';
                break;
            case '90d':
                $startDate = Carbon::today()->subDays(89);
                $groupBy = 'day';
                break;
            case '1y':
                $startDate = Carbon::today()->subYear()->addMonth(); // Last 12 full months + current
                $groupBy = 'month';
                break;
            case '7d':
            default:
                $startDate = Carbon::today()->subDays(6);
                $groupBy = 'day';
                break;
        }
        
        // A. Activity (Success vs Error)
        $msgLogs = (clone $logQuery)
            ->whereBetween('happened_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->get(); // Get usage data to memory to avoid SQL Date diffs
            
        \Illuminate\Support\Facades\Log::info('Chart Debug', [
            'total_logs_found' => $msgLogs->count(),
            'range' => $range,
            'start' => $startDate->toDateTimeString(),
            'end' => $endDate->toDateTimeString(),
            'sample_log' => $msgLogs->first() ? $msgLogs->first()->toArray() : null
        ]);

        $chartActivity = [
            'labels' => [],
            'success' => [],
            'error' => []
        ];

        if ($groupBy === 'day') {
            // Generate Labels (YYYY-MM-DD)
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $dt) {
                $dateStr = $dt->format('Y-m-d');
                $chartActivity['labels'][] = $dateStr;
                
                // Count from Collection
                $chartActivity['success'][] = $msgLogs->filter(function($log) use ($dateStr) {
                    return $log->happened_at->format('Y-m-d') === $dateStr && $log->status === 'success';
                })->count();
                
                $chartActivity['error'][] = $msgLogs->filter(function($log) use ($dateStr) {
                    return $log->happened_at->format('Y-m-d') === $dateStr && $log->status === 'error';
                })->count();
            }
        } else {
            // Generate Labels (YYYY-MM)
            $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);
            foreach ($period as $dt) {
                $lbl = $dt->format('Y-m');
                $chartActivity['labels'][] = $lbl;
                
                $chartActivity['success'][] = $msgLogs->filter(fn($l) => $l->happened_at->format('Y-m') === $lbl && $l->status === 'success')->count();
                $chartActivity['error'][] = $msgLogs->filter(fn($l) => $l->happened_at->format('Y-m') === $lbl && $l->status === 'error')->count();
            }
        }


        // B. Service Distribution
        $serviceStats = (clone $logQuery)
            ->join('api_services', 'api_logs.api_service_id', '=', 'api_services.id')
            ->selectRaw('api_services.name as name, count(*) as count')
            ->groupBy('api_services.name')
            ->pluck('count', 'name');

        $chartServices = [
            'labels' => $serviceStats->keys(),
            'data' => $serviceStats->values(),
        ];

        // 2. Live Feed (Paginated with Filters)
        $logsForFeed = clone $logQuery;
        
        // Apply Filters
        if ($request->filled('client_id')) {
            $logsForFeed->where('client_id', $request->input('client_id'));
        }
        
        if ($request->filled('user_id')) {
            $logsForFeed->whereHas('client', function($q) use ($request) {
                $q->where('user_id', $request->input('user_id'));
            });
        }
        
        if ($request->filled('service_id')) {
            $logsForFeed->where('api_service_id', $request->input('service_id'));
        }
        
        if ($request->filled('status')) {
            $logsForFeed->where('status', $request->input('status'));
        }
        
        $logs = $logsForFeed->with(['client.user', 'apiService'])
                      ->latest('happened_at')
                      ->paginate(15)
                      ->appends($request->except('page'));

        // Prepare filter data
        $filterClients = (clone $clientQuery)->orderBy('company')->get(['id', 'company']);

        $filterServices = \App\Models\ApiService::orderBy('name')->get(['id', 'name']);

        // User context selector (for Analysts)
        $contextUsers = $isGlobal ? \App\Models\User::role('Operador')->orderBy('name')->get(['id', 'name']) : collect();

        return view('admin.api-dashboard', compact('stats', 'logs', 'chartActivity', 'chartServices', 'filterClients', 'filterUsers', 'filterServices', 'contextUsers', 'selectedUser'));
    }
}
