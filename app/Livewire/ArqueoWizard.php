<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\WorkflowExecution;
use App\Models\WorkflowFileBatch;
use App\Models\WorkflowType;
use App\Models\Conciliation\ConciliationShift;
use App\Models\Conciliation\ConciliationGetnetTransaction;
use App\Models\Conciliation\ConciliationMpTransaction;
use App\Models\Conciliation\ConciliationSystemSale;
use App\Models\Conciliation\ConciliationCashMovement;
use App\Models\Conciliation\ConciliationMpNegative;
use App\Models\Conciliation\ConciliationRefund;
use App\Services\WorkflowPythonApiService;
use App\Services\ArqueoResultService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Carbon\Carbon;

class ArqueoWizard extends Component
{
    // Selection state
    public ?int $selectedClientId = null;
    public ?int $selectedBranchId = null;
    public ?int $selectedYear = null;
    public ?int $selectedMonth = null;

    // Processing state
    public bool $isProcessing = false;
    public bool $showProgressModal = false;
    public string $currentProgress = '';
    public int $progressPercentage = 0;
    public ?string $failedStep = null;

    // Available data
    public int $availableExecutionsCount = 0;
    public array $availableMonths = [];

    protected $rules = [
        'selectedClientId' => 'required|exists:clients,id',
        'selectedYear' => 'required|integer|min:2020|max:2030',
        'selectedMonth' => 'required|integer|min:1|max:12',
    ];

    protected $messages = [
        'selectedClientId.required' => 'Debe seleccionar una empresa',
        'selectedYear.required' => 'Debe seleccionar un año',
        'selectedMonth.required' => 'Debe seleccionar un mes',
    ];

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
    }

    /**
     * When client changes, load available months with data
     */
    public function updatedSelectedClientId(): void
    {
        $this->selectedBranchId = null;
        $this->loadAvailableMonths();
    }

    /**
     * When branch changes, reload available months
     */
    public function updatedSelectedBranchId(): void
    {
        $this->loadAvailableMonths();
    }

    /**
     * When year/month changes, update execution count
     */
    public function updatedSelectedYear(): void
    {
        $this->updateExecutionsCount();
    }

    public function updatedSelectedMonth(): void
    {
        $this->updateExecutionsCount();
    }

    /**
     * Load available months with conciliation data for selected client
     */
    protected function loadAvailableMonths(): void
    {
        if (!$this->selectedClientId) {
            $this->availableMonths = [];
            $this->availableExecutionsCount = 0;
            return;
        }

        // Get executions for this client/branch with conciliation data
        $query = WorkflowExecution::query()
            ->whereHas('fileBatch', function ($q) {
                $q->where('client_id', $this->selectedClientId);
                if ($this->selectedBranchId) {
                    $q->where('branch_id', $this->selectedBranchId);
                }
                // Only conciliation workflows (not arqueo)
                $q->whereHas('workflowType', function ($wq) {
                    $wq->whereIn('code', ['conciliacion', 'conciliacion_arqueo']);
                });
            })
            ->where('status', 'success')
            ->whereNotNull('completed_at');

        // Get distinct months
        $executions = $query->get();
        $months = [];

        foreach ($executions as $execution) {
            $monthKey = $execution->completed_at->format('Y-m');
            if (!isset($months[$monthKey])) {
                $months[$monthKey] = [
                    'year' => $execution->completed_at->year,
                    'month' => $execution->completed_at->month,
                    'label' => $execution->completed_at->translatedFormat('F Y'),
                    'count' => 0,
                ];
            }
            $months[$monthKey]['count']++;
        }

        // Sort by date descending
        krsort($months);
        $this->availableMonths = array_values($months);

        $this->updateExecutionsCount();
    }

    /**
     * Update count of available executions for selected period
     */
    protected function updateExecutionsCount(): void
    {
        if (!$this->selectedClientId || !$this->selectedYear || !$this->selectedMonth) {
            $this->availableExecutionsCount = 0;
            return;
        }

        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $this->availableExecutionsCount = $this->getExecutionsQuery($startDate, $endDate)->count();
    }

    /**
     * Get executions query for the selected period
     */
    protected function getExecutionsQuery(Carbon $startDate, Carbon $endDate)
    {
        return WorkflowExecution::query()
            ->whereHas('fileBatch', function ($q) {
                $q->where('client_id', $this->selectedClientId);
                if ($this->selectedBranchId) {
                    $q->where('branch_id', $this->selectedBranchId);
                }
                $q->whereHas('workflowType', function ($wq) {
                    $wq->whereIn('code', ['conciliacion', 'conciliacion_arqueo']);
                });
            })
            ->where('status', 'success')
            ->whereBetween('completed_at', [$startDate, $endDate]);
    }

    /**
     * Get existing arqueo executions for the selected period (to check for duplicates)
     */
    protected function getExistingArqueoExecutions(Carbon $startDate, Carbon $endDate)
    {
        return WorkflowExecution::query()
            ->whereHas('fileBatch', function ($q) {
                $q->where('client_id', $this->selectedClientId);
                if ($this->selectedBranchId) {
                    $q->where('branch_id', $this->selectedBranchId);
                }
                $q->whereHas('workflowType', function ($wq) {
                    $wq->where('code', 'arqueo');
                });
            })
            ->where('status', 'success')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->get();
    }

    /**
     * Submit arqueo request
     */
    public function submitArqueo(): void
    {
        $this->validate();

        try {
            $this->isProcessing = true;
            $this->showProgressModal = true;
            $this->failedStep = null;

            $this->updateProgress('Recopilando datos de conciliación...', 10);

            $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            // Check for existing arqueo in this period
            $existingArqueos = $this->getExistingArqueoExecutions($startDate, $endDate);
            if ($existingArqueos->isNotEmpty()) {
                Log::info('Arqueo existente encontrado para el período', [
                    'client_id' => $this->selectedClientId,
                    'period' => $startDate->format('Y-m'),
                    'existing_count' => $existingArqueos->count(),
                ]);
            }

            // Get all conciliation executions for the period
            $executions = $this->getExecutionsQuery($startDate, $endDate)->get();

            if ($executions->isEmpty()) {
                $this->failedStep = 'No hay datos de conciliación';
                $this->isProcessing = false;
                $this->addError('submit', 'No se encontraron datos de conciliación para el período seleccionado.');
                return;
            }

            $this->updateProgress('Preparando datos para envío...', 30);

            // Collect all conciliation data from executions
            $conciliationData = $this->collectConciliationData($executions);

            $this->updateProgress('Enviando datos al servidor de arqueo...', 50);

            // Get arqueo workflow type
            $arqueoType = WorkflowType::where('code', 'arqueo')->first();
            if (!$arqueoType) {
                throw new \Exception('Workflow type "arqueo" no encontrado');
            }

            // Create batch for arqueo
            $batch = WorkflowFileBatch::create([
                'workflow_type_id' => $arqueoType->id,
                'client_id' => $this->selectedClientId,
                'branch_id' => $this->selectedBranchId,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'uploaded_at' => now(),
                'files_metadata' => [
                    'source' => 'database',
                    'period' => $startDate->format('Y-m'),
                    'executions_count' => $executions->count(),
                ],
            ]);

            // Create execution record
            $execution = WorkflowExecution::create([
                'workflow_id' => null,
                'workflow_file_batch_id' => $batch->id,
                'status' => 'processing',
                'json_sent' => $conciliationData,
                'started_at' => now(),
            ]);

            // Send to Python API
            $pythonService = app(WorkflowPythonApiService::class);
            $result = $pythonService->processArqueo($conciliationData, $execution->id);

            if ($result['success']) {
                $this->updateProgress('Procesando resultados...', 80);

                $jsonData = $result['data'];

                $execution->update([
                    'status' => 'success',
                    'json_response' => $jsonData['data'] ?? [],
                    'completed_at' => now(),
                    'execution_time_ms' => now()->diffInMilliseconds($execution->started_at),
                ]);

                $batch->update(['status' => 'completed']);

                // Store arqueo results
                $this->processArqueoResults($execution, $jsonData);

                $this->updateProgress('Arqueo completado con éxito', 100);

                Log::info('Arqueo ejecutado con éxito', [
                    'execution_id' => $execution->id,
                    'client_id' => $this->selectedClientId,
                    'period' => $startDate->format('Y-m'),
                ]);

                session()->flash('success', 'Arqueo generado con éxito.');
                $this->redirect(route('programmer.arqueo.show', $execution), navigate: true);
            } else {
                $execution->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'logs_json' => ['error' => $result['error']],
                ]);

                $batch->update(['status' => 'failed']);

                $this->dispatchError('Error en el servidor', $result['error'] ?? 'Error desconocido al procesar el arqueo');
                $this->addError('submit', 'Error al procesar: ' . $result['error']);
            }
        } catch (\Exception $e) {
            Log::error('Error en submitArqueo: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            $this->dispatchError('Error inesperado', $e->getMessage());
            $this->addError('submit', 'Error al generar arqueo: ' . $e->getMessage());
        }
    }

    /**
     * Collect conciliation data from executions and format for API
     */
    protected function collectConciliationData($executions): array
    {
        $turnos = [];
        $getnet = [];
        $mp = [];
        $sistema = [];
        $ventasSistema = [];
        $cajaAdicion = [];
        $mpNegativos = [];
        $comandas = [];

        foreach ($executions as $execution) {
            // Collect shifts (turnos)
            foreach ($execution->conciliationShifts as $shift) {
                $turnos[] = $this->formatShiftForApi($shift);
            }

            // Collect Getnet transactions
            foreach ($execution->conciliationGetnetTransactions as $transaction) {
                $getnet[] = $this->formatGetnetForApi($transaction);
            }

            // Collect MP transactions
            foreach ($execution->conciliationMpTransactions as $transaction) {
                $mp[] = $this->formatMpForApi($transaction);
            }

            // Collect system sales
            foreach ($execution->conciliationSystemSales as $sale) {
                $sistema[] = $this->formatSystemSaleForApi($sale);
                $ventasSistema[] = $this->formatVentaSistemaForApi($sale);
            }

            // Collect cash movements
            foreach ($execution->conciliationCashMovements as $movement) {
                $cajaAdicion[] = $this->formatCashMovementForApi($movement);
            }

            // Collect MP negatives
            foreach ($execution->conciliationMpNegatives as $negative) {
                $mpNegativos[] = $this->formatMpNegativeForApi($negative);
            }

            // Collect refunds (comandas anuladas)
            foreach ($execution->conciliationRefunds as $refund) {
                $comandas[] = $this->formatRefundForApi($refund);
            }
        }

        return [
            'turnos' => $turnos,
            'getnet' => $getnet,
            'mp' => $mp,
            'sistema' => $sistema,
            'ventas_sistema' => $ventasSistema,
            'caja_adicion' => $cajaAdicion,
            'mp_negativos' => $mpNegativos,
            'comandas' => $comandas,
        ];
    }

    /**
     * Format shift for API
     */
    protected function formatShiftForApi($shift): array
    {
        return [
            'Fecha' => $shift->fecha_apertura?->format('Y-m-d\TH:i:s'),
            'Fecha Apertura' => $shift->fecha_apertura?->format('Y-m-d\TH:i:s'),
            'Hs Ap. Caja' => $shift->hora_apertura,
            'Fecha Cierre' => $shift->fecha_cierre?->format('Y-m-d\TH:i:s'),
            'Hs Cierre Caja' => $shift->hora_cierre,
            'TURNO' => $shift->turno,
            'Encargado' => $shift->encargado,
            'APERTURA CAJA Efectivo' => (float) $shift->apertura_caja,
            'Recuento Efectivo' => (float) $shift->recuento_efectivo,
            'Cantidad de comensales' => (int) $shift->cantidad_comensales,
        ];
    }

    /**
     * Format Getnet transaction for API
     */
    protected function formatGetnetForApi($transaction): array
    {
        return [
            'Nro de Establecimiento' => $transaction->nro_establecimiento,
            'Nombre Establecimiento' => $transaction->nombre_establecimiento,
            'Fecha de operacion' => $transaction->fecha_operacion?->format('d/m/Y H:i:s'),
            'Marca' => $transaction->marca,
            'Tipo' => $transaction->tipo_tarjeta,
            'Tarjeta' => $transaction->tarjeta_ultimos4,
            'Tipo de Transaccion' => $transaction->tipo_transaccion,
            'Estado venta' => $transaction->estado_venta,
            'Monto Bruto Transaccion' => (float) $transaction->monto_bruto,
            'Monto Neto Transaccion' => (float) $transaction->monto_neto,
            'TURNO' => $transaction->turno,
            'Estado' => $transaction->estado_conciliacion,
            'Tipo Match' => $transaction->tipo_match,
            'ID Venta Sistema (Conc.)' => $transaction->id_venta_sistema,
            'Fecha (Solo)' => $transaction->fecha_solo?->format('d/m/Y'),
        ];
    }

    /**
     * Format MP transaction for API
     */
    protected function formatMpForApi($transaction): array
    {
        return [
            'ID DE OPERACIÓN EN MERCADO PAGO' => $transaction->id_operacion_mp,
            'TIPO DE MEDIO DE PAGO' => $transaction->metodo_pago,
            'MEDIO DE PAGO' => $transaction->medio_pago,
            'FECHA DE ORIGEN' => $transaction->fecha?->format('d/m/Y') . ' ' . $transaction->hora,
            'MONTO NETO DE LA OPERACIÓN QUE IMPACTÓ TU DINERO' => (float) $transaction->monto_neto,
            'TURNO' => $transaction->turno,
            'Estado' => $transaction->estado_conciliacion ?? $transaction->estado,
            'Tipo Match' => $transaction->tipo_match,
            'ID Venta Sistema (Conc.)' => $transaction->id_venta_sistema,
            'Fecha (Solo)' => $transaction->fecha_solo?->format('d/m/Y'),
        ];
    }

    /**
     * Format system sale for API
     */
    protected function formatSystemSaleForApi($sale): array
    {
        return [
            'Fecha' => $sale->fecha_hora?->format('d/m/Y H:i:s'),
            'ID de venta' => $sale->id_ticket,
            'Total' => (float) $sale->monto_total,
            'Medio de cobro' => $sale->metodo_pago,
            'Monto Bruto pago' => (float) $sale->monto_total,
            'TURNO' => $sale->turno,
            'Estado' => $sale->estado_conciliacion,
            'Tipo Match' => $sale->tipo_match,
            'ID Operación MP (Conc.)' => $sale->id_operacion_conciliado ?? '',
            'Fecha (Solo)' => $sale->fecha_solo?->format('d/m/Y'),
        ];
    }

    /**
     * Format venta sistema for API
     */
    protected function formatVentaSistemaForApi($sale): array
    {
        return array_merge($this->formatSystemSaleForApi($sale), [
            'Estado_Auditoria' => $sale->estado_venta ?? 'Pendiente',
        ]);
    }

    /**
     * Format cash movement for API
     */
    protected function formatCashMovementForApi($movement): array
    {
        return [
            'Fecha Contable' => $movement->fecha_contable?->format('Y-m-d\TH:i:s'),
            'Fecha' => $movement->fecha_modificacion?->format('Y-m-d\TH:i:s'),
            'Origen' => $movement->origen,
            'Clase' => $movement->clase,
            'Proveedor / Para' => $movement->proveedor_para,
            'Monto' => (float) $movement->monto,
            'Comentario' => $movement->comentario,
            'Usuario' => $movement->usuario,
            'Tipo' => $movement->tipo,
            'Forma de Pago' => $movement->forma_pago,
            'TURNO' => $movement->turno,
        ];
    }

    /**
     * Format MP negative for API
     */
    protected function formatMpNegativeForApi($negative): array
    {
        return [
            'Fecha' => $negative->fecha?->format('d/m/Y'),
            'Hora' => $negative->hora,
            'ID DE OPERACIÓN EN MERCADO PAGO' => $negative->id_operacion_mp,
            'MONTO NETO DE LA OPERACIÓN QUE IMPACTÓ TU DINERO' => (float) $negative->monto_neto,
            'TURNO' => $negative->turno,
        ];
    }

    /**
     * Format refund for API
     */
    protected function formatRefundForApi($refund): array
    {
        return [
            'Producto' => $refund->producto,
            'Precios' => (float) $refund->precio,
            'Comentario' => $refund->comentario,
            'Fecha Hora pedido' => $refund->fecha_hora_pedido?->format('Y-m-d\TH:i:s'),
            'Hora pedido' => $refund->hora_pedido,
            'Hora Anulación' => $refund->hora_anulacion,
            'TURNO' => $refund->turno,
        ];
    }

    /**
     * Process and store arqueo results
     */
    protected function processArqueoResults(WorkflowExecution $execution, array $jsonData): void
    {
        try {
            $arqueoService = app(ArqueoResultService::class);

            $response = [
                'status' => 'success',
                'data' => $jsonData['data'] ?? $jsonData,
            ];

            if ($arqueoService->validateResponse($response)) {
                $stats = $arqueoService->processAndSave($execution, $response);

                Log::info('Arqueo results processed from wizard', [
                    'execution_id' => $execution->id,
                    'total_processed' => $stats['total_processed'] ?? 0,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process arqueo results from wizard', [
                'execution_id' => $execution->id,
                'error' => $e->getMessage(),
            ]);
            $this->dispatchError('Error al almacenar datos', 'Error guardando resultados del arqueo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update progress state
     */
    protected function updateProgress(string $message, int $percentage): void
    {
        $this->currentProgress = $message;
        $this->progressPercentage = $percentage;
        $this->dispatch('workflow-progress', [
            'message' => $message,
            'percentage' => $percentage,
        ]);
    }

    /**
     * Dispatch error event for modal
     */
    protected function dispatchError(string $step, string $message): void
    {
        $this->failedStep = $step;
        $this->isProcessing = false;
        $this->dispatch('workflow-error', [
            'step' => $step,
            'message' => $message,
        ]);
    }

    /**
     * Close progress modal
     */
    public function closeProgressModal(): void
    {
        $this->showProgressModal = false;
        $this->failedStep = null;
        $this->isProcessing = false;
        $this->currentProgress = '';
        $this->progressPercentage = 0;
    }

    /**
     * Retry arqueo execution
     */
    public function retryWorkflow(): void
    {
        $this->closeProgressModal();
        $this->resetErrorBag();
        $this->submitArqueo();
    }

    public function render()
    {
        $clients = Client::whereNull('parent_id')->orderBy('company')->get();

        $branches = $this->selectedClientId
            ? Client::where('parent_id', $this->selectedClientId)->orderBy('branch_name')->get()
            : collect();

        $years = range(now()->year, now()->year - 3);
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create(null, $i, 1)->translatedFormat('F');
        }

        return view('livewire.arqueo-wizard', [
            'clients' => $clients,
            'branches' => $branches,
            'years' => $years,
            'months' => $months,
        ])->layout('layouts.app');
    }
}
