<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Branch;
use App\Models\WorkflowType;
use App\Models\WorkflowFileBatch;
use App\Models\WorkflowUploadedFile;
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
            $tempBatch->setRelation('branch', Branch::find($this->selectedBranchId));
            $tempBatch->setRelation('user', auth()->user());
            
            // Build metadata only (don't read files for preview)
            $this->jsonPreview = [
                'metadata' => [
                    'workflow_type' => $tempBatch->workflowType->name,
                    'client_name' => $tempBatch->client->name ?? null,
                    'branch_name' => $tempBatch->branch->name ?? null,
                    'uploaded_by' => $tempBatch->user->name ?? null,
                    'total_files' => count($this->uploadedFiles),
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
            // Create batch
            $batch = WorkflowFileBatch::create([
                'workflow_type_id' => $this->selectedWorkflowTypeId,
                'client_id' => $this->selectedClientId,
                'branch_id' => $this->selectedBranchId,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'uploaded_at' => now(),
            ]);
            
            // Save files
            foreach ($this->uploadedFiles as $index => $file) {
                $this->saveUploadedFile($batch, $file, $index);
            }
            
            // Update batch status to validated
            $batch->update([
                'status' => 'validated',
                'validated_at' => now(),
            ]);
            
            // Redirect to batch detail
            session()->flash('success', 'Archivos cargados exitosamente. Batch: ' . $batch->batch_code);
            return redirect()->route('workflows.batch.show', $batch);
            
        } catch (\Exception $e) {
            $this->addError('submit', 'Error al guardar: ' . $e->getMessage());
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
        $clients = Client::orderBy('name')->get();
        $branches = $this->selectedClientId 
            ? Branch::where('client_id', $this->selectedClientId)->orderBy('name')->get()
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
        ]);
    }
}
