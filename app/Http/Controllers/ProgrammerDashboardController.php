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
            'total_clients' => Client::count(),
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

        // Recent Workflows
        $recentBatches = WorkflowFileBatch::with(['workflowType', 'client', 'user'])
            ->latest('uploaded_at')
            ->take(5)
            ->get();

        return view('programmer.dashboard', compact(
            'stats', 
            'recentBatches'
        ));
    }
}
