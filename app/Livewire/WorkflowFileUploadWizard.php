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
    
    // Step 3: Files
    public array $uploadedFiles = [];
    public array $fileMatches = []; // Map uploaded files to definitions
    public array $validationErrors = [];
    
    // Step 4: Confirmation
    public ?array $jsonPreview = null;
    
    protected $listeners = ['fileUploaded' => 'handleFileUpload'];
    
    /**
     * Navigate to next step
     */
    public function nextStep(): void
    {
        if ($this->validateCurrentStep()) {
            $this->currentStep++;
            
            // Generate JSON preview when reaching step 4
            if ($this->currentStep === 4) {
                $this->generateJsonPreview();
            }
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
     * Generate JSON preview for step 4
     */
    protected function generateJsonPreview(): void
    {
        try {
            $jsonService = app(WorkflowJsonGeneratorService::class);
            
            // Create temporary batch for preview
            $tempBatch = new WorkflowFileBatch([
                'workflow_type_id' => $this->selectedWorkflowTypeId,
                'client_id' => $this->selectedClientId,
                'branch_id' => $this->selectedBranchId,
                'user_id' => auth()->id(),
                'batch_code' => 'PREVIEW-' . now()->format('YmdHis'),
                'uploaded_at' => now(),
            ]);
            
            $tempBatch->setRelation('workflowType', WorkflowType::find($this->selectedWorkflowTypeId));
            $tempBatch->setRelation('client', Client::find($this->selectedClientId));
            $tempBatch->setRelation('branch', Client::find($this->selectedBranchId));
            $tempBatch->setRelation('user', auth()->user());
            
            // Build Data preview
            $filesData = [];
            foreach ($this->uploadedFiles as $index => $file) {
                $definitionId = $this->fileMatches[$index] ?? null;
                if ($definitionId) {
                    $definition = WorkflowFileDefinition::find($definitionId);
                    if ($definition) {
                        try {
                            // Read first 3 rows for preview
                            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new \Maatwebsite\Excel\HeadingRowImport(), $file->getRealPath());
                            $allRows = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $file->getRealPath());
                            $sheetData = $allRows[0] ?? [];
                            
                            if (count($sheetData) > 0) {
                                $headers = array_shift($sheetData);
                                $sampleRows = array_slice($sheetData, 0, 3); // Limit to 3 rows for preview
                                
                                $filesData[$definition->file_identifier] = array_map(function ($row) use ($headers) {
                                    $result = [];
                                    foreach ($headers as $idx => $header) {
                                        $result[$header] = $row[$idx] ?? null;
                                    }
                                    return $result;
                                }, $sampleRows);
                            } else {
                                $filesData[$definition->file_identifier] = [];
                            }
                        } catch (\Exception $e) {
                            $filesData[$definition->file_identifier] = ['error' => 'No se pudo leer el archivo'];
                        }
                    }
                }
            }

            // Build full preview
            $this->jsonPreview = [
                'Data' => $filesData,
                'metadata' => [
                    'client_id' => $tempBatch->client_id,
                    'branch_id' => $tempBatch->branch_id,
                    'uploaded_at' => $tempBatch->uploaded_at->toIso8601String(),
                    'workflow_type' => $tempBatch->workflowType->name,
                ],
            ];
        } catch (\Exception $e) {
            $this->jsonPreview = ['error' => $e->getMessage()];
        }
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
            
            \Illuminate\Support\Facades\Log::info('Batch creado: ' . $batch->batch_code);
            
            // 2. Save files
            foreach ($this->uploadedFiles as $index => $file) {
                $this->saveUploadedFile($batch, $file, $index);
            }
            
            \Illuminate\Support\Facades\Log::info('Archivos guardados físicamente');
            
            // 3. Update batch status to validated
            $batch->update([
                'status' => 'validated',
                'validated_at' => now(),
            ]);

            // 4. Generate FULL Master JSON
            \Illuminate\Support\Facades\Log::info('Generando JSON Maestro...');
            $jsonService = app(WorkflowJsonGeneratorService::class);
            $masterJson = $jsonService->generateFromBatch($batch);
            \Illuminate\Support\Facades\Log::info('JSON Maestro generado con éxito');

            // 5. Create Mock Execution
            $execution = WorkflowExecution::create([
                'workflow_id' => null,
                'workflow_file_batch_id' => $batch->id,
                'status' => 'success',
                'json_sent' => $masterJson,
                'json_response' => [
                    'status' => 'success',
                    'message' => 'Workflow ejecutado correctamente (Mock)',
                    'data' => [
                        'processed_rows' => count($masterJson['Data']['Turnos'] ?? []),
                        'rules_applied' => ['reconcile_sales', 'validate_payments']
                    ]
                ],
                'execution_time_ms' => 1250,
                'started_at' => now()->subSeconds(2),
                'completed_at' => now(),
                'logs_json' => ['info' => 'Ejecución de prueba generada automáticamente']
            ]);
            
            \Illuminate\Support\Facades\Log::info('Ejecución mock registrada. Redirigiendo...');
            
            // 6. Redirect to test view
            session()->flash('success', 'Workflow ejecutado con éxito. Visualizando JSON maestro.');
            return redirect()->route('programmer.workflows.test');
            
        } catch (\Exception $e) {
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
