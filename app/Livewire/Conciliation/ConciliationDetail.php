<?php

namespace App\Livewire\Conciliation;

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

class ConciliationDetail extends Component
{
    use WithPagination;

    public WorkflowExecution $execution;
    public string $activeTab = 'arqueo';
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
        'activeTab' => ['except' => 'arqueo'],
        'search' => ['except' => ''],
        'filterTurno' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterMetodoPago' => ['except' => ''],
        'filterTipo' => ['except' => ''],
        'filterFecha' => ['except' => ''],
        'sortField' => ['except' => ''],
        'sortDirection' => ['except' => 'desc'],
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

    public function mount(WorkflowExecution $execution)
    {
        $this->execution = $execution;
        $this->sortField = $this->defaultSortFields[$this->activeTab] ?? 'id';
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
     * Apply single date filter to a query
     */
    private function applyDateFilter($query, string $dateField): void
    {
        if ($this->filterFecha) {
            $query->whereDate($dateField, '=', $this->filterFecha);
        }
    }

    public function render()
    {
        $data = [];

        switch ($this->activeTab) {
            case 'arqueo':
                $data = $this->getArqueoData();
                break;

            case 'resumen':
                $data['summaries'] = $this->execution->conciliationSummaries()
                    ->orderBy('fecha')
                    ->get();
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

        // Get counts for tabs
        $counts = [
            'arqueo' => $this->execution->conciliationSummaries()->count(),
            'resumen' => $this->execution->conciliationSummaries()->count(),
            'getnet' => $this->execution->conciliationGetnetTransactions()->count(),
            'mp' => $this->execution->conciliationMpTransactions()->count(),
            'sistema' => $this->execution->conciliationSystemSales()->count(),
            'caja' => $this->execution->conciliationCashMovements()->count(),
            'turnos' => $this->execution->conciliationShifts()->count(),
            'devoluciones' => $this->execution->conciliationRefunds()->count(),
            'mp_negativos' => $this->execution->conciliationMpNegatives()->count(),
        ];

        // Get filter options
        $filterOptions = $this->getFilterOptions();

        return view('livewire.conciliation.detail', array_merge($data, [
            'counts' => $counts,
            'filterOptions' => $filterOptions,
        ]))->layout('layouts.app');
    }

    private function getArqueoData(): array
    {
        $query = $this->execution->conciliationSummaries();

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
        $this->applyDateFilter($query, 'fecha');

        // Sort
        $sortField = $this->sortField ?: 'fecha';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'arqueos' => $query->paginate($this->perPage),
        ];
    }

    private function getGetnetData(): array
    {
        $query = $this->execution->conciliationGetnetTransactions();

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
        $this->applyDateFilter($query, 'fecha_operacion');

        // Sort
        $sortField = $this->sortField ?: 'fecha_operacion';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'transactions' => $query->paginate($this->perPage),
            'estadosGetnet' => ConciliationGetnetTransaction::where('workflow_execution_id', $this->execution->id)
                ->distinct()
                ->pluck('estado_conciliacion')
                ->filter()
                ->values(),
        ];
    }

    private function getMpData(): array
    {
        $query = $this->execution->conciliationMpTransactions();

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
        $this->applyDateFilter($query, 'fecha');

        // Sort
        $sortField = $this->sortField ?: 'fecha';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'transactions' => $query->paginate($this->perPage),
            'estadosMp' => ConciliationMpTransaction::where('workflow_execution_id', $this->execution->id)
                ->distinct()
                ->pluck('estado_conciliacion')
                ->filter()
                ->values(),
        ];
    }

    private function getSistemaData(): array
    {
        $query = $this->execution->conciliationSystemSales();

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
        $this->applyDateFilter($query, 'fecha_hora');

        // Sort
        $sortField = $this->sortField ?: 'fecha_hora';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'sales' => $query->paginate($this->perPage),
            'metodosPago' => ConciliationSystemSale::where('workflow_execution_id', $this->execution->id)
                ->distinct()
                ->pluck('metodo_pago')
                ->filter()
                ->values(),
            'estadosSistema' => ConciliationSystemSale::where('workflow_execution_id', $this->execution->id)
                ->distinct()
                ->pluck('estado_conciliacion')
                ->filter()
                ->values(),
        ];
    }

    private function getCajaData(): array
    {
        $query = $this->execution->conciliationCashMovements();

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
        $this->applyDateFilter($query, 'fecha_contable');

        // Sort
        $sortField = $this->sortField ?: 'fecha_contable';
        $query->orderBy($sortField, $this->sortDirection);

        // Calculate totals (from all records, not filtered)
        $allMovements = $this->execution->conciliationCashMovements;

        return [
            'movements' => $query->paginate($this->perPage),
            'totalIngresos' => $allMovements->where('tipo', 'Ingreso')->sum('monto'),
            'totalEgresos' => $allMovements->where('tipo', 'Egreso')->sum('monto'),
            'tiposCaja' => ConciliationCashMovement::where('workflow_execution_id', $this->execution->id)
                ->distinct()
                ->pluck('tipo')
                ->filter()
                ->values(),
        ];
    }

    private function getTurnosData(): array
    {
        $query = $this->execution->conciliationShifts();

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
        $this->applyDateFilter($query, 'fecha_apertura');

        // Sort
        $sortField = $this->sortField ?: 'fecha_apertura';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'shifts' => $query->paginate($this->perPage),
        ];
    }

    private function getDevolucionesData(): array
    {
        $query = $this->execution->conciliationRefunds();

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
        $this->applyDateFilter($query, 'fecha_hora_pedido');

        // Sort
        $sortField = $this->sortField ?: 'fecha_hora_pedido';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'refunds' => $query->paginate($this->perPage),
        ];
    }

    private function getMpNegativosData(): array
    {
        $query = $this->execution->conciliationMpNegatives();

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
        $this->applyDateFilter($query, 'fecha');

        // Sort
        $sortField = $this->sortField ?: 'fecha';
        $query->orderBy($sortField, $this->sortDirection);

        return [
            'negatives' => $query->paginate($this->perPage),
        ];
    }

    private function getFilterOptions(): array
    {
        // Get turnos from conciliation_shifts table for this execution
        $turnosFromShifts = ConciliationShift::where('workflow_execution_id', $this->execution->id)
            ->distinct()
            ->pluck('turno')
            ->filter()
            ->values();

        // Get available dates from shifts
        $fechasDisponibles = ConciliationShift::where('workflow_execution_id', $this->execution->id)
            ->distinct()
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
