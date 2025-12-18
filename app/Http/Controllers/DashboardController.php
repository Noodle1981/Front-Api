<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApiLog;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Attention Required (Clientes con errores en las últimas 24hs)
        $attentionRequired = $user->clients()
            ->whereHas('apiLogs', function($q) {
                $q->where('status', 'error')
                  ->where('happened_at', '>=', now()->subDay());
            })
            ->withCount(['apiLogs' => function($q) {
                 $q->where('status', 'error')
                   ->where('happened_at', '>=', now()->subDay());
            }])
            ->limit(5)
            ->get();

        // 2. Stats Summary
        $stats = [
            'total_clients' => $user->clients()->count(),
            'syncs_today'   => ApiLog::whereHas('client', fn($q) => $q->where('user_id', $user->id))
                                    ->where('status', 'success')
                                    ->whereDate('happened_at', now())
                                    ->count(),
            'total_tx'      => Transaction::whereHas('client', fn($q) => $q->where('user_id', $user->id))->count()
        ];

        // 3. Recent Activity (Feed)
        $recentActivity = ApiLog::whereHas('client', fn($q) => $q->where('user_id', $user->id))
                            ->with('client', 'apiService')
                            ->latest('happened_at')
                            ->take(10)
                            ->get();

        return view('dashboard', compact('attentionRequired', 'stats', 'recentActivity'));
    }
}