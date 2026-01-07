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
        $mockData = WorkflowMockService::getMockConciliacionData();
        $data = $mockData['data'];

        $logoPath = public_path('img/logo.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        return view('pdfs.conciliacion.preview', [
            'execution' => $execution,
            'data' => $data,
            'metadata' => $data['metadata'],
            'is_pdf' => false,
            'logo_base64' => $logoBase64,
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
        
        $logoPath = public_path('img/logo.png');
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoBase64 = 'data:image/png;base64,' . $logoData;

        $pdf = Pdf::loadView('pdfs.conciliacion.main', [
            'execution' => $execution,
            'data' => $data,
            'metadata' => $data['metadata'],
            'is_pdf' => true,
            'logo_base64' => $logoBase64,
        ]);
        
        // Dompdf configuration for better performance and reliability
        $pdf->setPaper('a4', 'portrait');
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('defaultFont', 'Arial');
        
        // Nombre de archivo dinámico
        $fecha = str_replace('/', '-', $data['metadata']['fecha']);
        $sucursal = strtolower(str_replace(' ', '_', $data['metadata']['sucursal']));
        $filename = "arqueo_caja_{$fecha}_{$sucursal}.pdf";
        
        return $pdf->download($filename);
    }
}
