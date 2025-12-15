<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // --- KPIs de Resumen General ---
        $clientCount = $user->clients()->count();

        // Pasamos todos los datos a la vista
        return view('dashboard', compact('clientCount'));
    }
}