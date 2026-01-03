<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\WorkflowExecution;
use App\Services\WorkflowPdfService;
use Livewire\Component;
use Livewire\WithPagination;

class WorkflowHistoryTable extends Component
{
    use WithPagination;
    
    public string $searchClient = '';
    public string $searchStatus = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public int $perPage = 10;
    
    protected $queryString = [
        'searchClient' => ['except' => ''],
        'searchStatus' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];
    
    /**
     * Reset pagination when filters change
     */
    public function updatingSearchClient()
    {
        $this->resetPage();
    }
    
    public function updatingSearchStatus()
    {
        $this->resetPage();
    }
    
    public function updatingDateFrom()
    {
        $this->resetPage();
    }
    
    public function updatingDateTo()
    {
        $this->resetPage();
    }
    
    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->searchClient = '';
        $this->searchStatus = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }
    
    /**
     * Download PDF for execution
     */
    public function downloadPdf(int $executionId)
    {
        try {
            $execution = WorkflowExecution::findOrFail($executionId);
            $pdfService = app(WorkflowPdfService::class);
            
            return $pdfService->downloadExecutionReport($execution);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
    
    public function render()
    {
        $query = WorkflowExecution::with([
            'fileBatch.workflowType',
            'fileBatch.client',
            'fileBatch.branch',
            'fileBatch.user',
        ])->latest('created_at');
        
        // Filter by client
        if ($this->searchClient) {
            $query->whereHas('fileBatch', function ($q) {
                $q->where('client_id', $this->searchClient);
            });
        }
        
        // Filter by status
        if ($this->searchStatus) {
            $query->where('status', $this->searchStatus);
        }
        
        // Filter by date range
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        
        $executions = $query->paginate($this->perPage);
        $clients = Client::orderBy('name')->get();
        
        return view('livewire.workflow-history-table', [
            'executions' => $executions,
            'clients' => $clients,
        ]);
    }
}
