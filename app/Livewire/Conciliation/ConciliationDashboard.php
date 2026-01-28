<?php

namespace App\Livewire\Conciliation;

use App\Models\Client;
use App\Models\WorkflowExecution;
use App\Models\Conciliation\ConciliationSummary;
use Livewire\Component;

class ConciliationDashboard extends Component
{
    public string $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        // No pagination to reset
    }

    public function render()
    {
        // Get clients that have conciliation data
        $clientsWithConciliation = Client::query()
            ->whereHas('conciliationSummaries')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('company', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('company')
            ->get()
            ->map(function ($client) {
                // Get latest execution with conciliation data for this client
                $latestExecution = WorkflowExecution::whereHas('conciliationSummaries', function ($q) use ($client) {
                        $q->where('client_id', $client->id);
                    })
                    ->latest()
                    ->first();

                // Get summary stats for this client
                $summaries = ConciliationSummary::where('client_id', $client->id);

                $totalVentas = (clone $summaries)->sum('ventas_totales');
                $totalTurnos = (clone $summaries)->count();
                $fechaMin = (clone $summaries)->min('fecha');
                $fechaMax = (clone $summaries)->max('fecha');

                // Calculate conciliation percentages
                $mpVentasReal = (clone $summaries)->sum('mp_ventas_real');
                $mpConciliado = (clone $summaries)->sum('mp_conciliado');
                $mpPct = $mpVentasReal > 0 ? round(($mpConciliado / $mpVentasReal) * 100, 1) : 100;

                $getnetVentasReal = (clone $summaries)->sum('getnet_ventas_real');
                $getnetConciliado = (clone $summaries)->sum('getnet_conciliado');
                $getnetPct = $getnetVentasReal > 0 ? round(($getnetConciliado / $getnetVentasReal) * 100, 1) : 100;

                // Count alerts
                $alertas = $summaries->where(function ($q) {
                    $q->whereRaw('ABS(mp_diferencia) > 1000')
                      ->orWhereRaw('ABS(getnet_diferencia) > 1000')
                      ->orWhereRaw('ABS(efectivo_diferencia) > 1000');
                })->count();

                return [
                    'client' => $client,
                    'latest_execution' => $latestExecution,
                    'total_ventas' => $totalVentas,
                    'total_turnos' => $totalTurnos,
                    'fecha_min' => $fechaMin,
                    'fecha_max' => $fechaMax,
                    'mp_pct' => $mpPct,
                    'getnet_pct' => $getnetPct,
                    'alertas' => $alertas,
                ];
            });

        // Global KPIs
        $totalClientes = $clientsWithConciliation->count();
        $totalVentasGlobal = $clientsWithConciliation->sum('total_ventas');
        $totalTurnosGlobal = $clientsWithConciliation->sum('total_turnos');
        $totalAlertas = $clientsWithConciliation->sum('alertas');

        return view('livewire.conciliation.dashboard', [
            'clientsWithConciliation' => $clientsWithConciliation,
            'totalClientes' => $totalClientes,
            'totalVentasGlobal' => $totalVentasGlobal,
            'totalTurnosGlobal' => $totalTurnosGlobal,
            'totalAlertas' => $totalAlertas,
        ])->layout('layouts.app');
    }
}
