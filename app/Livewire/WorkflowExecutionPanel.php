<?php

namespace App\Livewire;

use App\Models\WorkflowFileBatch;
use App\Models\WorkflowExecution;
use App\Services\WorkflowExecutionService;
use Livewire\Component;

class WorkflowExecutionPanel extends Component
{
    public WorkflowFileBatch $batch;
    public bool $isExecuting = false;
    public ?WorkflowExecution $execution = null;
    public ?string $errorMessage = null;
    
    public function mount(WorkflowFileBatch $batch)
    {
        $this->batch = $batch;
        
        // Load latest execution if exists
        $this->execution = $batch->executions()->latest()->first();
    }
    
    /**
     * Execute the workflow
     */
    public function executeWorkflow()
    {
        $this->isExecuting = true;
        $this->errorMessage = null;
        
        try {
            $executionService = app(WorkflowExecutionService::class);
            $this->execution = $executionService->execute($this->batch);
            
            // Refresh batch
            $this->batch->refresh();
            
            $this->dispatch('execution-completed');
            
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        } finally {
            $this->isExecuting = false;
        }
    }
    
    public function render()
    {
        return view('livewire.workflow-execution-panel');
    }
}
