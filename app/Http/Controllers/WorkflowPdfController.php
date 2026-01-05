<?php

namespace App\Http\Controllers;

use App\Models\WorkflowExecution;
use App\Services\WorkflowMockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WorkflowPdfController extends Controller
{
    /**
     * Show PDF preview in browser
     */
    public function preview(WorkflowExecution $execution)
    {
        // TODO: Replace with actual data from execution->response_data
        // For now, use mock data
        $data = WorkflowMockService::getMockConciliacionData();
        
        return view('pdfs.workflow-conciliacion-preview', [
            'execution' => $execution,
            'data' => $data['data'],
            'metadata' => $data['data']['metadata'],
        ]);
    }

    /**
     * Download PDF file
     */
    public function download(WorkflowExecution $execution)
    {
        // TODO: Replace with actual data from execution->response_data
        // For now, use mock data
        $mockData = WorkflowMockService::getMockConciliacionData();
        $data = $mockData['data'];
        
        $pdf = Pdf::loadView('pdfs.workflow-conciliacion-pdf', [
            'execution' => $execution,
            'data' => $data,
            'metadata' => $data['metadata'],
        ]);
        
        $pdf->setPaper('a4', 'portrait');
        
        $filename = "conciliacion_{$execution->fileBatch->batch_code}.pdf";
        
        return $pdf->download($filename);
    }
}
