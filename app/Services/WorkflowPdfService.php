<?php

namespace App\Services;

use App\Models\WorkflowExecution;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkflowPdfService
{
    /**
     * Generate execution report PDF
     */
    public function generateExecutionReport(WorkflowExecution $execution): string
    {
        $data = $this->buildPdfData($execution);
        
        $pdf = Pdf::loadView('pdfs.workflow-execution', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');
        
        // Generate filename
        $filename = "workflow_execution_{$execution->fileBatch->batch_code}.pdf";
        $path = storage_path("app/workflows/pdfs/{$filename}");
        
        // Ensure directory exists
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save PDF
        $pdf->save($path);
        
        return $path;
    }
    
    /**
     * Build data array for PDF
     */
    public function buildPdfData(WorkflowExecution $execution): array
    {
        $batch = $execution->fileBatch;
        $results = $execution->logs_json ?? [];
        
        return [
            'execution' => $execution,
            'batch' => $batch,
            'workflow_type' => $batch->workflowType,
            'client' => $batch->client,
            'branch' => $batch->branch,
            'user' => $batch->user,
            'files' => $batch->uploadedFiles,
            'results' => $results,
            'is_success' => $execution->status === 'success',
            'generated_at' => now(),
        ];
    }
    
    /**
     * Download PDF directly
     */
    public function downloadExecutionReport(WorkflowExecution $execution): \Illuminate\Http\Response
    {
        $data = $this->buildPdfData($execution);
        
        $pdf = Pdf::loadView('pdfs.workflow-execution', $data);
        $pdf->setPaper('a4', 'portrait');
        
        $filename = "workflow_execution_{$execution->fileBatch->batch_code}.pdf";
        
        return $pdf->download($filename);
    }
}
