<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;

/**
 * Controlador para el panel de administración
 */

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'activeUsers' => User::where('is_active', true)->count(),
            'totalClients' => Client::count(),
            // 'activeDeals' => Deal::where('status', 'open')->count(), // Removed
            // 'pipelineValue' => Deal::where('status', 'open')->sum('value'), // Removed
        ];

        // Top Vendedores y Leads Logic eliminados

        return view('admin.dashboard', compact('stats'));
    }

    // public function stats() ... removed
    // private function getActivityColor/Icon ... removed

    public function logs()
    {
        // Activity logic removed as the model was deleted.
        // Returning empty view or redirecting.
        return redirect()->route('admin.dashboard')->with('status', 'Log system under maintenance.');
    }
}