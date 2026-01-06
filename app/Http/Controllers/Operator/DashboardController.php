<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkflowRequest;
use App\Models\WorkflowFileBatch;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $clientIds = $user->clients()->pluck('id');

        // 1. Attention Required (Workflows habilitados hoy)
        // Por ahora simulamos si hay batches completados recientemente
        $recentlyCompleted = WorkflowFileBatch::whereIn('client_id', $clientIds)
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDay())
            ->exists();

        // 2. Stats Summary
        $stats = [
            'total_clients' => $user->clients()->count(),
            'requested_workflows' => WorkflowRequest::where('user_id', $user->id)->count(),
            'executed_workflows' => WorkflowFileBatch::whereIn('client_id', $clientIds)->count(),
            'pdf_downloaded' => WorkflowFileBatch::whereIn('client_id', $clientIds)
                                    ->where('status', 'completed')
                                    ->count()
        ];

        // 3. Recent Activity (Historial de Workflows)
        $recentActivity = WorkflowFileBatch::whereIn('client_id', $clientIds)
                            ->with('client', 'workflowType')
                            ->latest('uploaded_at')
                            ->take(10)
                            ->get();

        return view('operator.dashboard', compact('recentlyCompleted', 'stats', 'recentActivity'));
    }
}
