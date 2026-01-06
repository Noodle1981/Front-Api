<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\ApiLog;
use App\Models\ClientCredential;
use App\Models\WorkflowFileBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgrammerDashboardController extends Controller
{
    public function index()
    {
        // Global Stats
        $stats = [
            'total_users' => User::role('Operador')->count(),
            'total_clients' => Client::count(),
            'workflows_sent' => WorkflowFileBatch::count(),
            'pdf_reports' => WorkflowFileBatch::where('status', 'completed')->count(), 
        ];

        // System Health Status (Based on Workflow Batches)
        $totalBatches = WorkflowFileBatch::count();
        $failedBatches = WorkflowFileBatch::where('status', 'failed')->count();
        
        $errorRate = $totalBatches > 0 ? ($failedBatches / $totalBatches) * 100 : 0;
        
        $stats['system_health'] = $errorRate < 5 ? 'healthy' : ($errorRate < 15 ? 'warning' : 'critical');
        $stats['error_rate'] = round($errorRate, 1);

        // Trend vs Last Week (Batches)
        $lastWeekFailed = WorkflowFileBatch::where('status', 'failed')
            ->whereBetween('uploaded_at', [now()->subWeeks(2), now()->subWeek()])
            ->count();
        $stats['trend'] = $failedBatches < $lastWeekFailed ? 'improving' : 'declining';

        // Workflow Requests (Pending for the Programmer to see)
        $pendingRequests = \App\Models\WorkflowRequest::where('status', 'pending')
            ->with(['user', 'client'])
            ->latest()
            ->get();

        // Simplified Users List
        $users = User::role('Operador')
            ->withCount('clients')
            ->get()
            ->map(function($user) {
                // Last activity (from batches)
                $lastBatch = WorkflowFileBatch::where('user_id', $user->id)
                    ->latest('uploaded_at')
                    ->first();
                $user->last_activity = $lastBatch ? $lastBatch->uploaded_at : null;
                $user->days_inactive = $lastBatch ? now()->diffInDays($lastBatch->uploaded_at) : 999;
                
                return $user;
            });

        // Recent Workflows
        $recentBatches = WorkflowFileBatch::with(['workflowType', 'client', 'user'])
            ->latest('uploaded_at')
            ->take(5)
            ->get();

        return view('programmer.dashboard', compact(
            'stats', 
            'users', 
            'pendingRequests',
            'recentBatches'
        ));
    }
}
