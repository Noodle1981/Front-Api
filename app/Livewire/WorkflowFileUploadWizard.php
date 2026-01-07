<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\WorkflowType;
use App\Models\WorkflowFileBatch;
use App\Models\WorkflowUploadedFile;
use App\Models\WorkflowExecution;
use App\Models\WorkflowFileDefinition;
use App\Services\FileValidationService;
use App\Services\WorkflowJsonGeneratorService;
use App\Services\WorkflowPythonApiService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

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
        
        if (!$this->selectedBranchId) {
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
     * Validate Step 3: Files upload
     */
    protected function validateStep3(): bool
    {
        if (empty($this->uploadedFiles)) {
            $this->addError('uploadedFiles', 'Debe cargar al menos un archivo');
            return false;
        }
        
        // Run validation service
        $this->validateFiles();
        
        return empty($this->validationErrors);
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
            
            // Step 1: Validate files (silently, without progress updates)
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
            $workflowType = WorkflowType::find($this->selectedWorkflowTypeId);
            
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
            
            $result = $pythonService->processFiles(
                $filesForApi,
                $workflowType->code ?? 'conciliacion',
                $execution->id
            );

            if ($result['success']) {
                // Update execution with success
                $execution->update([
                    'status' => 'success',
                    'excel_response_path' => $result['excel_path'],
                    'completed_at' => now(),
                    'execution_time_ms' => now()->diffInMilliseconds($execution->started_at),
                ]);

                // Update workflow request status if this execution came from a request
                if ($this->workflowRequestId) {
                    $workflowRequest = \App\Models\WorkflowRequest::find($this->workflowRequestId);
                    if ($workflowRequest) {
                        $workflowRequest->update(['status' => 'completed']);
                    }
                }
                
                \Illuminate\Support\Facades\Log::info('Workflow ejecutado con éxito');
                
                session()->flash('success', 'Workflow ejecutado con éxito.');
                return redirect()->route('programmer.workflows.execution.pdf.preview', $execution);
            } else {
                // Update execution with error
                $execution->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'logs_json' => ['error' => $result['error']],
                ]);

                $this->failedStep = 'Error en el servidor';
                $this->isProcessing = false;
                // Keep modal open to show the error
                $this->addError('submit', 'Error al procesar: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            $this->failedStep = 'Error desconocido';
            $this->isProcessing = false;
            // Keep modal open to show the error
            \Illuminate\Support\Facades\Log::error('Error en submitBatch: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
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
            
        $workflowTypes = WorkflowType::active()->get();
        $selectedWorkflow = $this->selectedWorkflowTypeId 
            ? WorkflowType::with('fileDefinitions')->find($this->selectedWorkflowTypeId)
            : null;
        
        return view('livewire.workflow-file-upload-wizard', [
            'clients' => $clients,
            'branches' => $branches,
            'workflowTypes' => $workflowTypes,
            'selectedWorkflow' => $selectedWorkflow,
        ])->layout('layouts.app');
    }
}
