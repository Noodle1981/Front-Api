<?php

namespace App\Livewire\Conciliation;

use App\Models\Client;
use App\Models\WorkflowExecution;
use App\Models\Conciliation\ConciliationSummary;
use App\Models\Conciliation\ConciliationGetnetTransaction;
use App\Models\Conciliation\ConciliationMpTransaction;
use App\Models\Conciliation\ConciliationSystemSale;
use App\Models\Conciliation\ConciliationCashMovement;
use App\Models\Conciliation\ConciliationShift;
use App\Models\Conciliation\ConciliationRefund;
use App\Models\Conciliation\ConciliationMpNegative;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ConciliationDetail extends Component
{
    use WithPagination;

    // Client and date range based filtering
    public ?int $clientId = null;
    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;
    public ?Client $client = null;

    // Legacy support for execution-based access
    public ?WorkflowExecution $execution = null;

    public string $activeTab = 'sistema';
    public string $search = '';
    public string $filterTurno = '';
    public string $filterEstado = '';
    public string $filterMetodoPago = '';
    public string $filterTipo = '';
    public string $filterFecha = '';
    public int $perPage = 25;

    // Sorting
    public string $sortField = '';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'activeTab' => ['except' => 'sistema'],
        'search' => ['except' => ''],
        'filterTurno' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterMetodoPago' => ['except' => ''],
        'filterTipo' => ['except' => ''],
        'filterFecha' => ['except' => ''],
        'sortField' => ['except' => ''],
        'sortDirection' => ['except' => 'desc'],
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
    ];

    // Default sort fields per tab
    protected array $defaultSortFields = [
        'arqueo' => 'fecha',
        'resumen' => 'fecha',
        'getnet' => 'fecha_operacion',
        'mp' => 'fecha',
        'sistema' => 'fecha_hora',
        'caja' => 'fecha_contable',
        'turnos' => 'fecha_apertura',
        'devoluciones' => 'fecha_hora_pedido',
        'mp_negativos' => 'fecha',
    ];

    // Date field names per tab for filtering
    protected array $dateFieldNames = [
        'arqueo' => 'fecha',
        'resumen' => 'fecha',
        'getnet' => 'fecha_operacion',
        'mp' => 'fecha',
        'sistema' => 'fecha_hora',
        'caja' => 'fecha_contable',
        'turnos' => 'fecha_apertura',
        'devoluciones' => 'fecha_hora_pedido',
        'mp_negativos' => 'fecha',
    ];

    /**
     * Mount component - supports both client-based and execution-based access
     */
    public function mount($client = null, $execution = null)
    {
        // If client is provided (new approach)
        if ($client) {
            if ($client instanceof Client) {
                $this->client = $client;
                $this->clientId = $client->id;
            } else {
                $this->client = Client::findOrFail($client);
                $this->clientId = $this->client->id;
            }

            // Set default date range from the latest execution's shifts
            $this->setDefaultDateRange();
        }
        // Legacy: if execution is provided
        elseif ($execution) {
            if ($execution instanceof WorkflowExecution) {
                $this->execution = $execution;
            } else {
                $this->execution = WorkflowExecution::findOrFail($execution);
            }

            // Extract client from execution
            $this->clientId = $this->execution->fileBatch?->branch_id
                ?? $this->execution->fileBatch?->client_id;

            if ($this->clientId) {
                $this->client = Client::find($this->clientId);
            }

            // Set date range from execution's shifts
            $this->setDateRangeFromExecution($this->execution);
        }

        // Set default tab based on whether arqueo data exists
        if ($this->activeTab === 'sistema' && $this->clientId) {
            // Check if arqueo data exists for this client
            $hasArqueoData = ConciliationSummary::where('client_id', $this->clientId)->exists();
            if ($hasArqueoData) {
                $this->activeTab = 'arqueo';
            }
        }

        $this->sortField = $this->defaultSortFields[$this->activeTab] ?? 'id';
    }

    /**
     * Set default date range from the latest execution's shifts
     */
    private function setDefaultDateRange(): void
    {
        if (!$this->clientId) {
            return;
        }

        // If dates not already set in query string
        if (!$this->fechaInicio || !$this->fechaFin) {
            // Get date range from summaries for this client
            $dateRange = ConciliationSummary::where('client_id', $this->clientId)
                ->selectRaw('MIN(fecha) as min_fecha, MAX(fecha) as max_fecha')
                ->first();

            if ($dateRange && $dateRange->min_fecha) {
                $this->fechaInicio = $this->fechaInicio ?: $dateRange->min_fecha;
                $this->fechaFin = $this->fechaFin ?: $dateRange->max_fecha;
            }
        }
    }

    /**
     * Set date range from a specific execution's shifts
     */
    private function setDateRangeFromExecution(WorkflowExecution $execution): void
    {
        $shifts = ConciliationShift::where('workflow_execution_id', $execution->id);

        $dateRange = $shifts->selectRaw('MIN(fecha_apertura) as min_fecha, MAX(fecha_apertura) as max_fecha')
            ->first();

        if ($dateRange && $dateRange->min_fecha) {
            $this->fechaInicio = $this->fechaInicio ?: Carbon::parse($dateRange->min_fecha)->format('Y-m-d');
            $this->fechaFin = $this->fechaFin ?: Carbon::parse($dateRange->max_fecha)->format('Y-m-d');
        }
    }

    /**
     * Update date range
     */
    public function updateDateRange(): void
    {
        $this->resetPage();
    }

    /**
     * Quick select: last month
     */
    public function selectLastMonth(): void
    {
        $this->fechaInicio = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    /**
     * Quick select: current month
     */
    public function selectCurrentMonth(): void
    {
        $this->fechaInicio = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->fechaFin = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->resetPage();
    }

    /**
     * Quick select: all data
     */
    public function selectAllData(): void
    {
        if (!$this->clientId) {
            return;
        }

        $dateRange = ConciliationSummary::where('client_id', $this->clientId)
            ->selectRaw('MIN(fecha) as min_fecha, MAX(fecha) as max_fecha')
            ->first();

        if ($dateRange && $dateRange->min_fecha) {
            $this->fechaInicio = $dateRange->min_fecha;
            $this->fechaFin = $dateRange->max_fecha;
        }
        $this->resetPage();
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->filterTurno = '';
        $this->filterEstado = '';
        $this->filterMetodoPago = '';
        $this->filterTipo = '';
        // Keep date filters when switching tabs
        $this->sortField = $this->defaultSortFields[$tab] ?? 'id';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTurno()
    {
        $this->resetPage();
    }

    public function updatingFilterEstado()
    {
        $this->resetPage();
    }

    public function updatingFilterMetodoPago()
    {
        $this->resetPage();
    }

    public function updatingFilterTipo()
    {
        $this->resetPage();
    }

    public function updatingFilterFecha()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterTurno = '';
        $this->filterEstado = '';
        $this->filterMetodoPago = '';
        $this->filterTipo = '';
        $this->filterFecha = '';
        $this->resetPage();
    }

    /**
     * Apply date range filter to a query (using client_id based filtering)
     */
    private function applyDateRangeFilter($query, string $dateField): void
    {
        if ($this->fechaInicio) {
            $query->whereDate($dateField, '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate($dateField, '<=', $this->fechaFin);
        }

        // Also apply single date filter if set
        if ($this->filterFecha) {
            $query->whereDate($dateField, '=', $this->filterFecha);
        }
    }

    /**
     * Get base query for a model filtering by client_id
     */
    private function getBaseQuery(string $modelClass)
    {
        return $modelClass::where('client_id', $this->clientId);
    }

    public function render()
    {
        $data = [];

        switch ($this->activeTab) {
            case 'arqueo':
                $data = $this->getArqueoData();
                break;

            case 'resumen':
                $query = $this->getBaseQuery(ConciliationSummary::class);
                $this->applyDateRangeFilter($query, 'fecha');
                $data['summaries'] = $query->orderBy('fecha')->get();
                break;

            case 'getnet':
                $data = $this->getGetnetData();
                break;

            case 'mp':
                $data = $this->getMpData();
                break;

            case 'sistema':
                $data = $this->getSistemaData();
                break;

            case 'caja':
                $data = $this->getCajaData();
                break;

            case 'turnos':
                $data = $this->getTurnosData();
                break;

            case 'devoluciones':
                $data = $this->getDevolucionesData();
                break;

            case 'mp_negativos':
                $data = $this->getMpNegativosData();
                break;
        }

        // Get counts for tabs (with date range filter)
        $counts = $this->getTabCounts();

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        // Get available months for quick selection
        $availableMonths = $this->getAvailableMonths();

        // Check if arqueo data exists (conciliation_summaries has data)
        $hasArqueoData = $counts['arqueo'] > 0;

        return view('livewire.conciliation.detail', array_merge($data, [
            'counts' => $counts,
            'filterOptions' => $filterOptions,
            'availableMonths' => $availableMonths,
            'hasArqueoData' => $hasArqueoData,
        ]))->layout('layouts.app');
    }

    /**
     * Get counts for all tabs with date range filter applied
     */
    private function getTabCounts(): array
    {
        $counts = [];

        // Arqueo/Resumen
        $query = $this->getBaseQuery(ConciliationSummary::class);
        $this->applyDateRangeFilter($query, 'fecha');
        $counts['arqueo'] = $query->count();
        $counts['resumen'] = $counts['arqueo'];

        // Getnet
        $query = $this->getBaseQuery(ConciliationGetnetTransaction::class);
        $this->applyDateRangeFilter($query, 'fecha_operacion');
        $counts['getnet'] = $query->count();

        // MP
        $query = $this->getBaseQuery(ConciliationMpTransaction::class);
        $this->applyDateRangeFilter($query, 'fecha');
        $counts['mp'] = $query->count();

        // Sistema
        $query = $this->getBaseQuery(ConciliationSystemSale::class);
        $this->applyDateRangeFilter($query, 'fecha_hora');
        $counts['sistema'] = $query->count();

        // Caja
        $query = $this->getBaseQuery(ConciliationCashMovement::class);
        $this->applyDateRangeFilter($query, 'fecha_contable');
        $counts['caja'] = $query->count();

        // Turnos
        $query = $this->getBaseQuery(ConciliationShift::class);
        $this->applyDateRangeFilter($query, 'fecha_apertura');
        $counts['turnos'] = $query->count();

        // Devoluciones
        $query = $this->getBaseQuery(ConciliationRefund::class);
        $this->applyDateRangeFilter($query, 'fecha_hora_pedido');
        $counts['devoluciones'] = $query->count();

        // MP Negativos
        $query = $this->getBaseQuery(ConciliationMpNegative::class);
        $this->applyDateRangeFilter($query, 'fecha');
        $counts['mp_negativos'] = $query->count();

        return $counts;
    }

    /**
     * Get available months with data for quick selection
     */
    private function getAvailableMonths(): array
    {
        if (!$this->clientId) {
            return [];
        }

        return ConciliationSummary::where('client_id', $this->clientId)
            ->selectRaw("strftime('%Y', fecha) as year, strftime('%m', fecha) as month, COUNT(*) as count")
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->limit(12)
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->year . '-' . $item->month);
                return [
                    'year' => $item->year,
                    'month' => $item->month,
                    'count' => $item->count,
                    'label' => $date->translatedFormat('M Y'),
                    'fecha_inicio' => $date->startOfMonth()->format('Y-m-d'),
                    'fecha_fin' => $date->endOfMonth()->format('Y-m-d'),
                ];
            })
            ->toArray();
    }

    /**
     * Select a specific month
     */
    public function selectMonth(string $fechaInicio, string $fechaFin): void
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->resetPage();
    }

    private function getArqueoData(): array
    {
        $query = $this->getBaseQuery(ConciliationSummary::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('encargado', 'like', "%{$this->search}%")
                    ->orWhere('dia', 'like', "%{$this->search}%")
                    ->orWhere('ventas_totales', 'like', "%{$this->search}%");
            });
        }

        // Filter by turno
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }

        // Filter by estado (efectivo_estado)
        if ($this->filterEstado) {
            $query->where('efectivo_estado', $this->filterEstado);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha');

        // Sort
        $sortField = $this->sortField ?: 'fecha';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'arqueos' => $query->paginate($this->perPage),
        ];
    }

    private function getGetnetData(): array
    {
        $query = $this->getBaseQuery(ConciliationGetnetTransaction::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('cod_transaccion', 'like', "%{$this->search}%")
                    ->orWhere('tarjeta_ultimos4', 'like', "%{$this->search}%")
                    ->orWhere('monto_bruto', 'like', "%{$this->search}%")
                    ->orWhere('marca', 'like', "%{$this->search}%")
                    ->orWhere('nro_cupon', 'like', "%{$this->search}%");
            });
        }

        // Filters
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }
        if ($this->filterEstado) {
            $query->where('estado_conciliacion', $this->filterEstado);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha_operacion');

        // Sort
        $sortField = $this->sortField ?: 'fecha_operacion';
        $query->orderBy($sortField, $this->sortDirection);

        // Get estados for filter
        $estadosQuery = $this->getBaseQuery(ConciliationGetnetTransaction::class);
        $this->applyDateRangeFilter($estadosQuery, 'fecha_operacion');

        return [
            'transactions' => $query->paginate($this->perPage),
            'estadosGetnet' => $estadosQuery->distinct()
                ->pluck('estado_conciliacion')
                ->filter()
                ->values(),
        ];
    }

    private function getMpData(): array
    {
        $query = $this->getBaseQuery(ConciliationMpTransaction::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id_operacion_mp', 'like', "%{$this->search}%")
                    ->orWhere('monto_neto', 'like', "%{$this->search}%")
                    ->orWhere('medio_pago', 'like', "%{$this->search}%");
            });
        }

        // Filters
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }
        if ($this->filterEstado) {
            $query->where('estado_conciliacion', $this->filterEstado);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha');

        // Sort
        $sortField = $this->sortField ?: 'fecha';
        $query->orderBy($sortField, $this->sortDirection);

        // Get estados for filter
        $estadosQuery = $this->getBaseQuery(ConciliationMpTransaction::class);
        $this->applyDateRangeFilter($estadosQuery, 'fecha');

        return [
            'transactions' => $query->paginate($this->perPage),
            'estadosMp' => $estadosQuery->distinct()
                ->pluck('estado_conciliacion')
                ->filter()
                ->values(),
        ];
    }

    private function getSistemaData(): array
    {
        $query = $this->getBaseQuery(ConciliationSystemSale::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id_ticket', 'like', "%{$this->search}%")
                    ->orWhere('monto_total', 'like', "%{$this->search}%");
            });
        }

        // Filters
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }
        if ($this->filterEstado) {
            $query->where('estado_conciliacion', $this->filterEstado);
        }
        if ($this->filterMetodoPago) {
            $query->where('metodo_pago', $this->filterMetodoPago);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha_hora');

        // Sort
        $sortField = $this->sortField ?: 'fecha_hora';
        $query->orderBy($sortField, $this->sortDirection);

        // Get filter options
        $baseQuery = $this->getBaseQuery(ConciliationSystemSale::class);
        $this->applyDateRangeFilter($baseQuery, 'fecha_hora');

        return [
            'sales' => $query->paginate($this->perPage),
            'metodosPago' => (clone $baseQuery)->distinct()
                ->pluck('metodo_pago')
                ->filter()
                ->values(),
            'estadosSistema' => (clone $baseQuery)->distinct()
                ->pluck('estado_conciliacion')
                ->filter()
                ->values(),
        ];
    }

    private function getCajaData(): array
    {
        $query = $this->getBaseQuery(ConciliationCashMovement::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('comentario', 'like', "%{$this->search}%")
                    ->orWhere('proveedor_para', 'like', "%{$this->search}%")
                    ->orWhere('monto', 'like', "%{$this->search}%")
                    ->orWhere('usuario', 'like', "%{$this->search}%");
            });
        }

        // Filters
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }
        if ($this->filterTipo) {
            $query->where('tipo', $this->filterTipo);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha_contable');

        // Sort
        $sortField = $this->sortField ?: 'fecha_contable';
        $query->orderBy($sortField, $this->sortDirection);

        // Calculate totals (with date range filter)
        $totalsQuery = $this->getBaseQuery(ConciliationCashMovement::class);
        $this->applyDateRangeFilter($totalsQuery, 'fecha_contable');
        $allMovements = $totalsQuery->get();

        // Get tipos for filter
        $tiposQuery = $this->getBaseQuery(ConciliationCashMovement::class);
        $this->applyDateRangeFilter($tiposQuery, 'fecha_contable');

        return [
            'movements' => $query->paginate($this->perPage),
            'totalIngresos' => $allMovements->where('tipo', 'Ingreso')->sum('monto'),
            'totalEgresos' => $allMovements->where('tipo', 'Egreso')->sum('monto'),
            'tiposCaja' => $tiposQuery->distinct()
                ->pluck('tipo')
                ->filter()
                ->values(),
        ];
    }

    private function getTurnosData(): array
    {
        $query = $this->getBaseQuery(ConciliationShift::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('encargado', 'like', "%{$this->search}%");
            });
        }

        // Filters
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha_apertura');

        // Sort
        $sortField = $this->sortField ?: 'fecha_apertura';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'shifts' => $query->paginate($this->perPage),
        ];
    }

    private function getDevolucionesData(): array
    {
        $query = $this->getBaseQuery(ConciliationRefund::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('producto', 'like', "%{$this->search}%")
                    ->orWhere('comentario', 'like', "%{$this->search}%")
                    ->orWhere('precio', 'like', "%{$this->search}%");
            });
        }

        // Filters
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha_hora_pedido');

        // Sort
        $sortField = $this->sortField ?: 'fecha_hora_pedido';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'refunds' => $query->paginate($this->perPage),
        ];
    }

    private function getMpNegativosData(): array
    {
        $query = $this->getBaseQuery(ConciliationMpNegative::class);

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id_operacion_mp', 'like', "%{$this->search}%")
                    ->orWhere('monto_neto', 'like', "%{$this->search}%");
            });
        }

        // Filters
        if ($this->filterTurno) {
            $query->where('turno', $this->filterTurno);
        }

        // Filter by date range
        $this->applyDateRangeFilter($query, 'fecha');

        // Sort
        $sortField = $this->sortField ?: 'fecha';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'negatives' => $query->paginate($this->perPage),
        ];
    }

    private function getFilterOptions(): array
    {
        // Get turnos from conciliation_shifts table for this client
        $turnosQuery = $this->getBaseQuery(ConciliationShift::class);
        $this->applyDateRangeFilter($turnosQuery, 'fecha_apertura');

        $turnosFromShifts = $turnosQuery->distinct()
            ->pluck('turno')
            ->filter()
            ->values();

        // Get available dates from shifts
        $fechasQuery = $this->getBaseQuery(ConciliationShift::class);
        $this->applyDateRangeFilter($fechasQuery, 'fecha_apertura');

        $fechasDisponibles = $fechasQuery->distinct()
            ->orderBy('fecha_apertura')
            ->pluck('fecha_apertura')
            ->filter()
            ->values();

        return [
            'turnos' => $turnosFromShifts,
            'fechas' => $fechasDisponibles,
        ];
    }
}
