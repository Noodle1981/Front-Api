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
    
    // Progress tracking
    public bool $isProcessing = false;
    public string $currentProgress = '';
    public int $progressPercentage = 0;
    
    protected $listeners = ['fileUploaded' => 'handleFileUpload'];
    
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
     * When files are uploaded, validate them
     */
    public function updatedUploadedFiles(): void
    {
        $this->validateFiles();
    }
    
    /**
     * Update progress message and percentage
     */
    protected function updateProgress(string $message, int $percentage): void
    {
        $this->currentProgress = $message;
        $this->progressPercentage = $percentage;
        $this->dispatch('progress-updated');
    }
    
    /**
     * Submit and save batch
     */
    public function submitBatch()
    {
        // Final validation
        if (!$this->validateCurrentStep()) {
            return;
        }
        
        try {
            $this->isProcessing = true;
            $this->updateProgress('Analizando tipo de archivo...', 10);
            
            \Illuminate\Support\Facades\Log::info('Iniciando submitBatch para workflow');
            
            // 1. Create batch
            $batch = WorkflowFileBatch::create([
                'workflow_type_id' => $this->selectedWorkflowTypeId,
                'client_id' => $this->selectedClientId,
                'branch_id' => $this->selectedBranchId,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'uploaded_at' => now(),
            ]);
            
            $this->updateProgress('Analizando archivos...', 20);
            \Illuminate\Support\Facades\Log::info('Batch creado: ' . $batch->batch_code);
            
            $this->updateProgress('Analizando contenido...', 40);
            
            // 2. Update batch status to validated
            $batch->update([
                'status' => 'validated',
                'validated_at' => now(),
            ]);

            // 3. Create execution record
            $execution = WorkflowExecution::create([
                'workflow_id' => null,
                'workflow_file_batch_id' => $batch->id,
                'status' => 'processing',
                'started_at' => now(),
            ]);

            $this->updateProgress('Ejecutando workflow...', 50);

            // 4. Prepare files for Python API (from uploaded files, not from storage)
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

            $this->updateProgress('Esperando respuesta del servidor...', 70);
            
            $result = $pythonService->processFiles(
                $filesForApi,
                $workflowType->code ?? 'conciliacion',
                $execution->id
            );

            $this->updateProgress('Generando reporte...', 90);

            if ($result['success']) {
                // Update execution with success
                $execution->update([
                    'status' => 'success',
                    'excel_response_path' => $result['excel_path'],
                    'completed_at' => now(),
                    'execution_time_ms' => now()->diffInMilliseconds($execution->started_at),
                ]);

                $this->updateProgress('¡Completado!', 100);
                
                \Illuminate\Support\Facades\Log::info('Workflow ejecutado con éxito');
                
                session()->flash('success', 'Workflow ejecutado con éxito.');
                return redirect()->route('programmer.workflows.history');
            } else {
                // Update execution with error
                $execution->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'logs_json' => ['error' => $result['error']],
                ]);

                $this->isProcessing = false;
                $this->addError('submit', 'Error al procesar: ' . $result['error']);
            }
            
        } catch (\Exception $e) {
            $this->isProcessing = false;
            \Illuminate\Support\Facades\Log::error('Error en submitBatch: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            $this->addError('submit', 'Error al guardar y ejecutar: ' . $e->getMessage());
        }
    }
    
    /**
     * Save individual uploaded file
     */
    protected function saveUploadedFile(WorkflowFileBatch $batch, $file, int $index): void
    {
        $definitionId = $this->fileMatches[$index] ?? null;
        
        if (!$definitionId) {
            throw new \Exception("No se pudo identificar el archivo: " . $file->getClientOriginalName());
        }
        
        $definition = $batch->workflowType->fileDefinitions()->find($definitionId);
        
        // Generate filename
        $extension = $file->getClientOriginalExtension();
        $filename = $batch->batch_code . '_' . $definition->file_identifier . '.' . $extension;
        
        // Store file
        $path = $file->storeAs("workflows/{$batch->id}", $filename);
        
        // Create record
        WorkflowUploadedFile::create([
            'workflow_file_batch_id' => $batch->id,
            'workflow_file_definition_id' => $definition->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $filename,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'validation_status' => 'valid',
        ]);
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
