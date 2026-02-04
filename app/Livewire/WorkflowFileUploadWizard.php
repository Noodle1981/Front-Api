<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\WorkflowType;
use App\Models\WorkflowFileBatch;
use App\Models\WorkflowUploadedFile;
use App\Models\WorkflowExecution;
use App\Models\WorkflowFileDefinition;
use App\Models\Conciliation\ConciliationSummary;
use App\Models\Conciliation\ConciliationGetnetTransaction;
use App\Models\Conciliation\ConciliationMpTransaction;
use App\Models\Conciliation\ConciliationCashMovement;
use App\Services\FileValidationService;
use App\Services\WorkflowJsonGeneratorService;
use App\Services\WorkflowPythonApiService;
use App\Services\ConciliacionDataService;
use App\Services\ArqueoDataService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WorkflowFileUploadWizard extends Component
{
    use WithFileUploads;
    
    // Wizard state
    public int $currentStep = 1;
    
    // Step 1: Client & Branch
    public ?int $selectedClientId = null;
    public ?int $selectedBranchId = null;
    
    // Step 2: Workflow Type
    public ?int $selectedWorkflowTypeId = null;
    
    // Step 3: Files + Processing
    public array $uploadedFiles = [];
    public array $fileMatches = []; // Map uploaded files to definitions
    public array $validationErrors = [];

    // Step 3 (Arqueo mode): Date range selection for existing conciliation data
    public ?string $arqueoFechaInicio = null;
    public ?string $arqueoFechaFin = null;
    public int $arqueoTurnosCount = 0;
    public bool $arqueoHasData = false;
    
    // Workflow Request (if executing from a request)
    public ?int $workflowRequestId = null;
    
    // Progress tracking
    public bool $isProcessing = false;
    public bool $showProgressModal = false;
    public string $currentProgress = '';
    public int $progressPercentage = 0;
    public ?string $failedStep = null; // Track which step failed
    
    protected $listeners = ['fileUploaded' => 'handleFileUpload'];
    
    /**
     * Mount component with optional workflow request
     */
    public function mount(?int $workflowRequestId = null): void
    {
        if ($workflowRequestId) {
            $this->workflowRequestId = $workflowRequestId;
            $request = \App\Models\WorkflowRequest::find($workflowRequestId);
            
            if ($request) {
                $this->selectedClientId = $request->client_id;
                $this->selectedBranchId = $request->branch_id;
                
                // Map workflow_type string to workflow_type_id
                $workflowType = \App\Models\WorkflowType::where('code', $request->workflow_type)->first();
                if ($workflowType) {
                    $this->selectedWorkflowTypeId = $workflowType->id;
                    $this->currentStep = 3; // Skip to file upload step
                }
            }
        }
    }
    
    /**
     * Navigate to next step
     */
    public function nextStep(): void
    {
        if ($this->validateCurrentStep()) {
            $this->currentStep++;
        }
    }
    
    /**
     * Navigate to previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }
    
    /**
     * Validate current step
     */
    protected function validateCurrentStep(): bool
    {
        return match($this->currentStep) {
            1 => $this->validateStep1(),
            2 => $this->validateStep2(),
            3 => $this->validateStep3(),
            default => true,
        };
    }
    
    /**
     * Validate Step 1: Client & Branch selection
     */
    protected function validateStep1(): bool
    {
        if (!$this->selectedClientId) {
            $this->addError('selectedClientId', 'Debe seleccionar un cliente');
            return false;
        }

        // Branch is only required if the client has branches
        $client = Client::find($this->selectedClientId);
        $hasBranches = $client && $client->children()->exists();

        if ($hasBranches && !$this->selectedBranchId) {
            $this->addError('selectedBranchId', 'Debe seleccionar una sede');
            return false;
        }

        return true;
    }
    
    /**
     * Validate Step 2: Workflow Type selection
     */
    protected function validateStep2(): bool
    {
        if (!$this->selectedWorkflowTypeId) {
            $this->addError('selectedWorkflowTypeId', 'Debe seleccionar un tipo de workflow');
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate Step 3: Files upload (or conciliation data for Arqueo)
     */
    protected function validateStep3(): bool
    {
        $workflowType = WorkflowType::find($this->selectedWorkflowTypeId);

        // For Arqueo workflow, validate conciliation data exists
        if ($this->isArqueoWorkflow($workflowType)) {
            return $this->validateArqueoData();
        }

        // For other workflows, validate files
        if (empty($this->uploadedFiles)) {
            $this->addError('uploadedFiles', 'Debe cargar al menos un archivo');
            return false;
        }

        // Run validation service
        $this->validateFiles();

        return empty($this->validationErrors);
    }

    /**
     * Validate that conciliation data exists for Arqueo workflow
     */
    protected function validateArqueoData(): bool
    {
        $clientId = $this->selectedBranchId ?? $this->selectedClientId;

        if (!$clientId) {
            $this->addError('arqueoData', 'Debe seleccionar un cliente');
            return false;
        }

        if (!$this->arqueoFechaInicio || !$this->arqueoFechaFin) {
            $this->addError('arqueoData', 'Debe seleccionar un periodo de fechas');
            return false;
        }

        // Check if conciliation data exists for this period
        $turnosCount = ConciliationSummary::where('client_id', $clientId)
            ->whereDate('fecha', '>=', $this->arqueoFechaInicio)
            ->whereDate('fecha', '<=', $this->arqueoFechaFin)
            ->count();

        if ($turnosCount === 0) {
            $this->addError('arqueoData', 'No hay datos de conciliación para el periodo seleccionado');
            return false;
        }

        $this->arqueoTurnosCount = $turnosCount;
        return true;
    }

    /**
     * Check conciliation data availability when dates change
     */
    public function checkArqueoDataAvailability(): void
    {
        $clientId = $this->selectedBranchId ?? $this->selectedClientId;

        if (!$clientId || !$this->arqueoFechaInicio || !$this->arqueoFechaFin) {
            $this->arqueoTurnosCount = 0;
            $this->arqueoHasData = false;
            return;
        }

        $this->arqueoTurnosCount = ConciliationSummary::where('client_id', $clientId)
            ->whereDate('fecha', '>=', $this->arqueoFechaInicio)
            ->whereDate('fecha', '<=', $this->arqueoFechaFin)
            ->count();

        $this->arqueoHasData = $this->arqueoTurnosCount > 0;
    }

    /**
     * Get available date range for conciliation data
     */
    public function getArqueoAvailableDateRange(): array
    {
        $clientId = $this->selectedBranchId ?? $this->selectedClientId;

        if (!$clientId) {
            return ['min' => null, 'max' => null];
        }

        $minDate = ConciliationSummary::where('client_id', $clientId)->min('fecha');
        $maxDate = ConciliationSummary::where('client_id', $clientId)->max('fecha');

        return [
            'min' => $minDate ? \Carbon\Carbon::parse($minDate)->format('Y-m-d') : null,
            'max' => $maxDate ? \Carbon\Carbon::parse($maxDate)->format('Y-m-d') : null,
        ];
    }

    /**
     * Update arqueo data availability when dates change
     */
    public function updatedArqueoFechaInicio(): void
    {
        $this->checkArqueoDataAvailability();
    }

    public function updatedArqueoFechaFin(): void
    {
        $this->checkArqueoDataAvailability();
    }

    /**
     * Get conciliation data for arqueo processing
     * Returns data in the format expected by Python API /arqueo endpoint
     */
    protected function getConciliationDataForArqueo(): array
    {
        $clientId = $this->selectedBranchId ?? $this->selectedClientId;

        // Get turnos (summaries)
        $turnos = ConciliationSummary::where('client_id', $clientId)
            ->whereDate('fecha', '>=', $this->arqueoFechaInicio)
            ->whereDate('fecha', '<=', $this->arqueoFechaFin)
            ->orderBy('fecha')
            ->get()
            ->map(function ($t) {
                return [
                    'fecha' => $t->fecha?->format('Y-m-d'),
                    'dia' => $t->dia,
                    'turno' => $t->turno,
                    'encargado' => $t->encargado,
                    'apertura' => $t->apertura?->format('H:i'),
                    'cierre' => $t->cierre?->format('H:i'),
                    'horas_trabajadas' => (float) $t->horas_trabajadas,
                    'ventas_totales' => (float) $t->ventas_totales,
                    'cantidad_comensales' => (int) $t->cantidad_comensales,
                    'ticket_promedio' => (float) $t->ticket_promedio,
                    'cantidad_tickets' => (int) $t->cantidad_tickets,
                    'propina' => (float) $t->propina,
                    'mp_ventas_real' => (float) $t->mp_ventas_real,
                    'mp_ventas_sistema' => (float) $t->mp_ventas_sistema,
                    'mp_conciliado' => (float) $t->mp_conciliado,
                    'mp_no_conciliado' => (float) $t->mp_no_conciliado,
                    'mp_diferencia' => (float) $t->mp_diferencia,
                    'mp_porcentaje' => (float) $t->mp_porcentaje,
                    'mp_estado' => $t->mp_estado,
                    'getnet_ventas_real' => (float) $t->getnet_ventas_real,
                    'getnet_ventas_sistema' => (float) $t->getnet_ventas_sistema,
                    'getnet_conciliado' => (float) $t->getnet_conciliado,
                    'getnet_no_conciliado' => (float) $t->getnet_no_conciliado,
                    'getnet_diferencia' => (float) $t->getnet_diferencia,
                    'getnet_porcentaje' => (float) $t->getnet_porcentaje,
                    'getnet_estado' => $t->getnet_estado,
                    'efectivo_total' => (float) $t->efectivo_total,
                    'efectivo_apertura_caja' => (float) $t->efectivo_apertura_caja,
                    'efectivo_recuento' => (float) $t->efectivo_recuento,
                    'efectivo_diferencia' => (float) $t->efectivo_diferencia,
                    'efectivo_porcentaje' => (float) $t->efectivo_porcentaje,
                    'efectivo_estado' => $t->efectivo_estado,
                    'cta_cte_total' => (float) $t->cta_cte_total,
                    'otros' => (float) $t->otros,
                    'descuentos' => (float) $t->descuentos,
                    'ventas_facturadas' => (float) $t->ventas_facturadas,
                    'ideal_facturacion' => (float) $t->ideal_facturacion,
                    'diferencia_facturacion' => (float) $t->diferencia_facturacion,
                    'porcentaje_facturacion' => (float) $t->porcentaje_facturacion,
                    'ventas_por_hora' => $t->ventas_por_hora ?? [],
                ];
            })
            ->toArray();

        // Get getnet transactions
        $getnet = ConciliationGetnetTransaction::where('client_id', $clientId)
            ->whereDate('fecha_operacion', '>=', $this->arqueoFechaInicio)
            ->whereDate('fecha_operacion', '<=', $this->arqueoFechaFin)
            ->orderBy('fecha_operacion')
            ->get()
            ->map(function ($t) {
                return [
                    'fecha_operacion' => $t->fecha_operacion?->format('Y-m-d H:i:s'),
                    'cod_transaccion' => $t->cod_transaccion,
                    'marca' => $t->marca,
                    'tipo_tarjeta' => $t->tipo_tarjeta,
                    'tarjeta_ultimos4' => $t->tarjeta_ultimos4,
                    'monto_bruto' => (float) $t->monto_bruto,
                    'monto_neto' => (float) $t->monto_neto,
                    'arancel' => (float) $t->arancel,
                    'estado_venta' => $t->estado_venta,
                    'estado_conciliacion' => $t->estado_conciliacion,
                    'tipo_match' => $t->tipo_match,
                    'turno' => $t->turno,
                ];
            })
            ->toArray();

        // Get MP transactions
        $mp = ConciliationMpTransaction::where('client_id', $clientId)
            ->whereDate('fecha', '>=', $this->arqueoFechaInicio)
            ->whereDate('fecha', '<=', $this->arqueoFechaFin)
            ->orderBy('fecha')
            ->get()
            ->map(function ($t) {
                return [
                    'fecha' => $t->fecha?->format('Y-m-d'),
                    'hora' => $t->hora,
                    'id_operacion_mp' => $t->id_operacion_mp,
                    'monto_neto' => (float) $t->monto_neto,
                    'medio_pago' => $t->medio_pago,
                    'metodo_pago' => $t->metodo_pago,
                    'estado' => $t->estado,
                    'estado_conciliacion' => $t->estado_conciliacion,
                    'tipo_match' => $t->tipo_match,
                    'turno' => $t->turno,
                ];
            })
            ->toArray();

        // Get cash movements
        $movimientos = ConciliationCashMovement::where('client_id', $clientId)
            ->whereDate('fecha_contable', '>=', $this->arqueoFechaInicio)
            ->whereDate('fecha_contable', '<=', $this->arqueoFechaFin)
            ->orderBy('fecha_contable')
            ->get()
            ->map(function ($m) {
                return [
                    'fecha_contable' => $m->fecha_contable?->format('Y-m-d'),
                    'tipo' => $m->tipo,
                    'proveedor_para' => $m->proveedor_para,
                    'monto' => (float) $m->monto,
                    'comentario' => $m->comentario,
                    'usuario' => $m->usuario,
                    'forma_pago' => $m->forma_pago,
                    'turno' => $m->turno,
                ];
            })
            ->toArray();

        return [
            'turnos' => $turnos,
            'getnet' => $getnet,
            'mp' => $mp,
            'movimientos_caja' => $movimientos,
            'fecha_inicio' => $this->arqueoFechaInicio,
            'fecha_fin' => $this->arqueoFechaFin,
            'client_id' => $clientId,
        ];
    }
    
    /**
     * Validate uploaded files
     */
    public function validateFiles(): void
    {
        if (empty($this->uploadedFiles)) {
            return;
        }
        
        $validationService = app(FileValidationService::class);
        $workflowType = WorkflowType::find($this->selectedWorkflowTypeId);
        
        if (!$workflowType) {
            return;
        }
        
        $fileDefinitions = $workflowType->fileDefinitions;
        
        // Match each uploaded file to a definition
        $this->fileMatches = [];
        foreach ($this->uploadedFiles as $index => $file) {
            $definition = $validationService->matchFileDefinition($file, $fileDefinitions);
            $this->fileMatches[$index] = $definition?->id;
        }
        
        // Check for validation errors
        $this->validationErrors = [];
        
        // Check file count
        if (count($this->uploadedFiles) !== $workflowType->expected_files_count) {
            $this->validationErrors[] = [
                'type' => 'file_count',
                'message' => "Se esperaban {$workflowType->expected_files_count} archivos, pero se cargaron " . count($this->uploadedFiles),
            ];
        }
        
        // Check for unmatched files
        foreach ($this->fileMatches as $index => $definitionId) {
            if (!$definitionId) {
                $this->validationErrors[] = [
                    'type' => 'unmatched',
                    'message' => "No se pudo identificar el archivo: " . $this->uploadedFiles[$index]->getClientOriginalName(),
                ];
            }
        }
        
        // Check for duplicates
        $definitionCounts = array_count_values(array_filter($this->fileMatches));
        foreach ($definitionCounts as $definitionId => $count) {
            if ($count > 1) {
                $definition = $fileDefinitions->find($definitionId);
                $this->validationErrors[] = [
                    'type' => 'duplicate',
                    'message' => "Archivo duplicado: {$definition->display_name}",
                ];
            }
        }
        
        // Check for missing files
        $uploadedDefinitionIds = array_filter($this->fileMatches);
        foreach ($fileDefinitions->where('is_required', true) as $definition) {
            if (!in_array($definition->id, $uploadedDefinitionIds)) {
                $this->validationErrors[] = [
                    'type' => 'missing',
                    'message' => "Falta el archivo: {$definition->display_name}",
                ];
            }
        }
    }
    
    /**
     * Remove uploaded file
     */
    public function removeFile(int $index): void
    {
        unset($this->uploadedFiles[$index]);
        unset($this->fileMatches[$index]);
        
        // Reindex arrays
        $this->uploadedFiles = array_values($this->uploadedFiles);
        $this->fileMatches = array_values($this->fileMatches);
        
        $this->validateFiles();
    }
    
    /**
     * When client changes, reset branch
     */
    public function updatedSelectedClientId(): void
    {
        $this->selectedBranchId = null;
    }
    
    /**
     * When files are uploaded, validate them immediately
     */
    public function updatedUploadedFiles(): void
    {
        // Validate files immediately to provide feedback
        $this->validateFiles();
    }
    
    /**
     * Submit and save batch
     */
    public function submitBatch()
    {
        try {
            $this->isProcessing = true;
            $this->showProgressModal = true; // Open modal
            
            // Show single progress message
            $this->updateProgress('Conectando con Servidor de Reglas de Negocio...', 50);

            $workflowType = WorkflowType::find($this->selectedWorkflowTypeId);

            // Step 1: Validate (files for regular workflows, conciliation data for Arqueo)
            if (!$this->isArqueoWorkflow($workflowType)) {
                // Validate files (silently, without progress updates)
                $this->validateFiles();

                // Check for validation errors
                if (!empty($this->validationErrors)) {
                    $this->failedStep = 'Validación de archivos';
                    $this->isProcessing = false;
                    // Keep modal open to show the error

                    // Add errors to the error bag
                    foreach ($this->validationErrors as $error) {
                        $this->addError('validation', $error['message']);
                    }
                    return;
                }
            }
            
            // Final step validation
            if (!$this->validateCurrentStep()) {
                $this->failedStep = 'Validación de archivos';
                $this->isProcessing = false;
                // Keep modal open to show the error
                return;
            }
            
            \Illuminate\Support\Facades\Log::info('Iniciando submitBatch para workflow');
            
            // 2. Create batch
            $batch = WorkflowFileBatch::create([
                'workflow_type_id' => $this->selectedWorkflowTypeId,
                'client_id' => $this->selectedClientId,
                'branch_id' => $this->selectedBranchId,
                'user_id' => auth()->id(),
                'workflow_request_id' => $this->workflowRequestId,
                'status' => 'pending',
                'uploaded_at' => now(),
            ]);
            
            \Illuminate\Support\Facades\Log::info('Batch creado: ' . $batch->batch_code);
            
            // 3. Save uploaded files to database with metadata
            $this->saveUploadedFilesToDatabase($batch);
            
            // 4. Update batch status
            $batch->update([
                'status' => 'validated',
                'validated_at' => now(),
            ]);

            // 5. Create execution record
            $execution = WorkflowExecution::create([
                'workflow_id' => null,
                'workflow_file_batch_id' => $batch->id,
                'status' => 'processing',
                'started_at' => now(),
            ]);

            // 6. Prepare files for Python API (from uploaded files, not from storage)
            $pythonService = app(WorkflowPythonApiService::class);

            // Map uploaded files to their identifiers
            $filesForApi = [];
            foreach ($this->uploadedFiles as $index => $file) {
                $definitionId = $this->fileMatches[$index] ?? null;
                if ($definitionId) {
                    $definition = WorkflowFileDefinition::find($definitionId);
                    if ($definition) {
                        $filesForApi[$definition->file_identifier] = $file;
                    }
                }
            }

            // Determine workflow type and call appropriate endpoint
            $isConciliacionSimple = $this->isConciliacionSimpleWorkflow($workflowType);
            $isConciliacionArqueo = $this->isConciliacionArqueoWorkflow($workflowType);
            $isArqueo = $this->isArqueoWorkflow($workflowType);

            if ($isArqueo) {
                // Arqueo workflow: uses existing conciliation data from database
                $this->processArqueoFromConciliationData($pythonService, $execution, $batch, $workflowType);
            } elseif ($isConciliacionSimple) {
                // New workflow: call /conciliar endpoint (JSON response)
                $this->processConciliacionSimple($pythonService, $filesForApi, $execution, $batch, $workflowType);
            } elseif ($isConciliacionArqueo) {
                // Original workflow: call /procesar endpoint (Excel response)
                $this->processConciliacionArqueo($pythonService, $filesForApi, $execution, $batch, $workflowType);
            } else {
                // Generic workflow processing
                $this->processGenericWorkflow($pythonService, $filesForApi, $execution, $batch, $workflowType);
            }
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en submitBatch: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            $this->dispatchError('Error inesperado', $e->getMessage());
            $this->addError('submit', 'Error al guardar y ejecutar: ' . $e->getMessage());
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
            'percentage' => $percentage
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
     * Retry workflow execution
     */
    public function retryWorkflow(): void
    {
        $this->closeProgressModal();
        $this->resetErrorBag();
        // Small delay to allow modal to close
        $this->submitBatch();
    }

    /**
     * Get mock conciliation response from test file
     */
    protected function getMockConciliacionResponse(): array
    {
        $testFile = base_path('arqueo_resultado_test.json');

        if (file_exists($testFile)) {
            $content = file_get_contents($testFile);
            $data = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        // Fallback to basic structure
        return [
            'status' => 'success',
            'data' => [
                'arqueo_por_turno' => [],
                'getnet_conciliado' => [],
                'mp_conciliado' => [],
                'sistema_conciliado' => [],
                'ventas_sistema' => [],
                'turnos_procesados' => [],
                'caja_adicion' => [],
                'devoluciones' => [],
                'mp_negativos' => [],
            ],
        ];
    }

    /**
     * Check if the workflow is "Conciliación" (new - JSON response)
     */
    protected function isConciliacionSimpleWorkflow(?WorkflowType $workflowType): bool
    {
        if (!$workflowType) {
            return false;
        }

        // Check by exact code
        return $workflowType->code === 'conciliacion';
    }

    /**
     * Check if the workflow is "Conciliación y Arqueo" (original - Excel response)
     */
    protected function isConciliacionArqueoWorkflow(?WorkflowType $workflowType): bool
    {
        if (!$workflowType) {
            return false;
        }

        // Check by exact code
        return $workflowType->code === 'conciliacion_arqueo';
    }

    /**
     * Check if the workflow is any conciliation workflow (for legacy compatibility)
     */
    protected function isConciliationWorkflow(?WorkflowType $workflowType): bool
    {
        return $this->isConciliacionSimpleWorkflow($workflowType)
            || $this->isConciliacionArqueoWorkflow($workflowType);
    }

    /**
     * Check if the workflow is "Arqueo" (uses existing conciliation data)
     */
    protected function isArqueoWorkflow(?WorkflowType $workflowType): bool
    {
        if (!$workflowType) {
            return false;
        }

        return $workflowType->code === 'arqueo';
    }

    /**
     * Process workflow "Conciliación" - calls /conciliar endpoint
     */
    protected function processConciliacionSimple(
        WorkflowPythonApiService $pythonService,
        array $filesForApi,
        WorkflowExecution $execution,
        WorkflowFileBatch $batch,
        WorkflowType $workflowType
    ): void {
        $result = $pythonService->processConciliar($filesForApi, $execution->id);

        if ($result['success']) {
            $jsonData = $result['data'];

            // Update execution with success and JSON response
            $execution->update([
                'status' => 'success',
                'json_response' => $jsonData['data'] ?? [],
                'completed_at' => now(),
                'execution_time_ms' => now()->diffInMilliseconds($execution->started_at),
            ]);

            $batch->update(['status' => 'completed']);

            if ($this->workflowRequestId) {
                $workflowRequest = \App\Models\WorkflowRequest::find($this->workflowRequestId);
                if ($workflowRequest) {
                    $workflowRequest->update(['status' => 'completed']);
                }
            }

            // Process conciliation data with the new service
            $this->processConciliacionData($execution, $jsonData);

            Log::info('Conciliación simple ejecutada con éxito', [
                'execution_id' => $execution->id,
            ]);

            session()->flash('success', 'Conciliación ejecutada con éxito.');
            $this->redirect(route('programmer.conciliacion.show', $execution), navigate: true);
        } else {
            $execution->update([
                'status' => 'failed',
                'completed_at' => now(),
                'logs_json' => ['error' => $result['error']],
            ]);

            $this->dispatchError('Error en el servidor', $result['error'] ?? 'Error desconocido al procesar la conciliación');
            $this->addError('submit', 'Error al procesar: ' . $result['error']);
        }
    }

    /**
     * Process workflow "Conciliación y Arqueo" - calls /procesar-json endpoint
     * Returns JSON with arqueo format (arqueo_resultado_test.json)
     */
    protected function processConciliacionArqueo(
        WorkflowPythonApiService $pythonService,
        array $filesForApi,
        WorkflowExecution $execution,
        WorkflowFileBatch $batch,
        WorkflowType $workflowType
    ): void {
        // Call /procesar-json for JSON response (arqueo format)
        $result = $pythonService->procesarJson($filesForApi, $execution->id);

        if ($result['success']) {
            $jsonData = $result['data'];

            $execution->update([
                'status' => 'success',
                'json_response' => $jsonData['data'] ?? [],
                'completed_at' => now(),
                'execution_time_ms' => now()->diffInMilliseconds($execution->started_at),
            ]);

            $batch->update(['status' => 'completed']);

            if ($this->workflowRequestId) {
                $workflowRequest = \App\Models\WorkflowRequest::find($this->workflowRequestId);
                if ($workflowRequest) {
                    $workflowRequest->update(['status' => 'completed']);
                }
            }

            // Process arqueo data with ArqueoDataService
            $this->processArqueoData($execution, $jsonData);

            Log::info('Conciliación y Arqueo ejecutada con éxito', [
                'execution_id' => $execution->id,
            ]);

            session()->flash('success', 'Workflow ejecutado con éxito.');
            $this->redirect(route('programmer.conciliacion.show', $execution), navigate: true);
        } else {
            $execution->update([
                'status' => 'failed',
                'completed_at' => now(),
                'logs_json' => ['error' => $result['error']],
            ]);

            $this->dispatchError('Error en el servidor', $result['error'] ?? 'Error desconocido al procesar el arqueo');
            $this->addError('submit', 'Error al procesar: ' . $result['error']);
        }
    }

    /**
     * Process workflow "Arqueo" - uses existing conciliation data from database
     * Sends JSON data to /arqueo endpoint
     */
    protected function processArqueoFromConciliationData(
        WorkflowPythonApiService $pythonService,
        WorkflowExecution $execution,
        WorkflowFileBatch $batch,
        WorkflowType $workflowType
    ): void {
        // Get conciliation data from database
        $conciliationData = $this->getConciliationDataForArqueo();

        Log::info('Procesando Arqueo desde datos de conciliación', [
            'execution_id' => $execution->id,
            'turnos_count' => count($conciliationData['turnos']),
            'getnet_count' => count($conciliationData['getnet']),
            'mp_count' => count($conciliationData['mp']),
            'fecha_inicio' => $this->arqueoFechaInicio,
            'fecha_fin' => $this->arqueoFechaFin,
        ]);

        // Call /arqueo endpoint with JSON data
        $result = $pythonService->processArqueo($conciliationData, $execution->id);

        if ($result['success']) {
            $jsonData = $result['data'];

            $execution->update([
                'status' => 'success',
                'json_response' => $jsonData['data'] ?? [],
                'completed_at' => now(),
                'execution_time_ms' => now()->diffInMilliseconds($execution->started_at),
            ]);

            $batch->update(['status' => 'completed']);

            if ($this->workflowRequestId) {
                $workflowRequest = \App\Models\WorkflowRequest::find($this->workflowRequestId);
                if ($workflowRequest) {
                    $workflowRequest->update(['status' => 'completed']);
                }
            }

            // Process arqueo data with ArqueoDataService
            $this->processArqueoData($execution, $jsonData);

            Log::info('Arqueo desde conciliación ejecutado con éxito', [
                'execution_id' => $execution->id,
            ]);

            session()->flash('success', 'Arqueo ejecutado con éxito.');
            $this->redirect(route('programmer.conciliacion.show', $execution), navigate: true);
        } else {
            $execution->update([
                'status' => 'failed',
                'completed_at' => now(),
                'logs_json' => ['error' => $result['error']],
            ]);

            $this->dispatchError('Error en el servidor', $result['error'] ?? 'Error desconocido al procesar el arqueo');
            $this->addError('submit', 'Error al procesar: ' . $result['error']);
        }
    }

    /**
     * Process generic workflow (non-conciliation)
     */
    protected function processGenericWorkflow(
        WorkflowPythonApiService $pythonService,
        array $filesForApi,
        WorkflowExecution $execution,
        WorkflowFileBatch $batch,
        WorkflowType $workflowType
    ): void {
        $result = $pythonService->processFiles(
            $filesForApi,
            $workflowType->code,
            $execution->id
        );

        if ($result['success']) {
            $execution->update([
                'status' => 'success',
                'excel_response_path' => $result['excel_path'],
                'completed_at' => now(),
                'execution_time_ms' => now()->diffInMilliseconds($execution->started_at),
            ]);

            $batch->update(['status' => 'completed']);

            if ($this->workflowRequestId) {
                $workflowRequest = \App\Models\WorkflowRequest::find($this->workflowRequestId);
                if ($workflowRequest) {
                    $workflowRequest->update(['status' => 'completed']);
                }
            }

            Log::info('Workflow genérico ejecutado con éxito', [
                'execution_id' => $execution->id,
            ]);

            session()->flash('success', 'Workflow ejecutado con éxito.');
            $this->redirect(route('programmer.workflows.execution.pdf.preview', $execution), navigate: true);
        } else {
            $execution->update([
                'status' => 'failed',
                'completed_at' => now(),
                'logs_json' => ['error' => $result['error']],
            ]);

            $this->dispatchError('Error en el servidor', $result['error'] ?? 'Error desconocido al procesar el workflow');
            $this->addError('submit', 'Error al procesar: ' . $result['error']);
        }
    }

    /**
     * Process and save conciliation data (new format from /conciliar)
     */
    protected function processConciliacionData(WorkflowExecution $execution, array $jsonData): void
    {
        try {
            $conciliacionService = app(ConciliacionDataService::class);

            $response = [
                'status' => 'success',
                'data' => $jsonData['data'] ?? $jsonData,
            ];

            if ($conciliacionService->validateResponse($response)) {
                $stats = $conciliacionService->processAndSave($execution, $response);

                Log::info('Conciliacion data processed from wizard', [
                    'execution_id' => $execution->id,
                    'total_processed' => $stats['total_processed'] ?? 0,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process conciliacion data from wizard', [
                'execution_id' => $execution->id,
                'error' => $e->getMessage(),
            ]);
            $this->dispatchError('Error al almacenar datos', 'Error guardando datos de conciliación: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process and save arqueo data (format from /procesar-json)
     */
    protected function processArqueoData(WorkflowExecution $execution, array $jsonData): void
    {
        try {
            $arqueoService = app(ArqueoDataService::class);

            $response = [
                'status' => 'success',
                'data' => $jsonData['data'] ?? $jsonData,
            ];

            if ($arqueoService->validateResponse($response)) {
                $stats = $arqueoService->processAndSave($execution, $response);

                Log::info('Arqueo data processed from wizard', [
                    'execution_id' => $execution->id,
                    'total_processed' => $stats['total_processed'] ?? 0,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process arqueo data from wizard', [
                'execution_id' => $execution->id,
                'error' => $e->getMessage(),
            ]);
            $this->dispatchError('Error al almacenar datos', 'Error guardando datos de arqueo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Save uploaded files to database with metadata
     */
    protected function saveUploadedFilesToDatabase(WorkflowFileBatch $batch): void
    {
        foreach ($this->uploadedFiles as $index => $file) {
            $definitionId = $this->fileMatches[$index] ?? null;
            if (!$definitionId) {
                continue;
            }
            
            $definition = WorkflowFileDefinition::find($definitionId);
            if (!$definition) {
                continue;
            }

            // Generate filename (virtual)
            $extension = $file->getClientOriginalExtension();
            $filename = $batch->batch_code . '_' . $definition->file_identifier . '.' . $extension;
            
            // NOTE: We do NOT store the file physically as per user requirement.
            // We only process it in memory to get metadata and pass to Python API.
            $path = 'MEMORY_ONLY'; 
            
            // Default metadata
            $columnsCount = 0;
            $rowsCount = 0;
            
            // Count columns and rows using PhpSpreadsheet
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();
                
                $columnsCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
                $rowsCount = $highestRow;
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("No se pudo analizar archivo {$file->getClientOriginalName()}: " . $e->getMessage());
            }
            
            // Create record
            WorkflowUploadedFile::create([
                'workflow_file_batch_id' => $batch->id,
                'workflow_file_definition_id' => $definition->id,
                'original_filename' => $file->getClientOriginalName(),
                'stored_filename' => $filename,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'rows_count' => $rowsCount,
                'columns_count' => $columnsCount,
                'validation_status' => 'valid',
            ]);
        }
    }
    
    public function render()
    {
        // Get parent clients (not branches)
        $clients = Client::whereNull('parent_id')->orderBy('company')->get();

        // Get branches (children) of selected client
        $branches = $this->selectedClientId
            ? Client::where('parent_id', $this->selectedClientId)->orderBy('branch_name')->get()
            : collect();

        // Check if the selected client has branches
        $clientHasBranches = $branches->isNotEmpty();

        $workflowTypes = WorkflowType::active()->get();
        $selectedWorkflow = $this->selectedWorkflowTypeId
            ? WorkflowType::with('fileDefinitions')->find($this->selectedWorkflowTypeId)
            : null;

        // Check if this is an Arqueo workflow
        $isArqueoWorkflow = $this->isArqueoWorkflow($selectedWorkflow);

        // Get available date range for Arqueo
        $arqueoDateRange = $isArqueoWorkflow ? $this->getArqueoAvailableDateRange() : ['min' => null, 'max' => null];

        // Check if conciliation data exists for the selected client
        $clientId = $this->selectedBranchId ?? $this->selectedClientId;
        $hasConciliationData = $clientId ? ConciliationSummary::where('client_id', $clientId)->exists() : false;

        return view('livewire.workflow-file-upload-wizard', [
            'clients' => $clients,
            'branches' => $branches,
            'clientHasBranches' => $clientHasBranches,
            'workflowTypes' => $workflowTypes,
            'selectedWorkflow' => $selectedWorkflow,
            'isArqueoWorkflow' => $isArqueoWorkflow,
            'arqueoDateRange' => $arqueoDateRange,
            'hasConciliationData' => $hasConciliationData,
        ])->layout('layouts.app');
    }
}
