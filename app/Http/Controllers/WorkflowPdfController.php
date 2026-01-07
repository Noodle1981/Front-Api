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
        
        return view('pdfs.conciliacion.main', [
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
        
        $pdf = Pdf::loadView('pdfs.conciliacion.main', [
            'execution' => $execution,
            'data' => $data,
            'metadata' => $data['metadata'],
        ]);
        
        // Configuración optimizada para Dompdf
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);  // Para imágenes/logos externos
        $pdf->setOption('isHtml5ParserEnabled', true);  // Mejor renderizado HTML5
        $pdf->setOption('isFontSubsettingEnabled', true);  // Optimizar fuentes
        
        // Nombre de archivo dinámico
        $fecha = str_replace('/', '-', $data['metadata']['fecha']);
        $sucursal = strtolower(str_replace(' ', '_', $data['metadata']['sucursal']));
        $filename = "arqueo_caja_{$fecha}_{$sucursal}.pdf";
        
        return $pdf->download($filename);
    }
}
