<?php

namespace App\Services;

use App\Models\WorkflowExecution;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkflowPdfService
{
    /**
     * Generate PDF from Excel execution result
     */
    public function generateFromExcel(WorkflowExecution $execution): string
    {
        if (!$execution->hasExcelResponse()) {
            throw new \Exception('No hay archivo Excel disponible para esta ejecución');
        }

        $excelPath = $execution->getExcelResponseFullPath();
        $spreadsheet = IOFactory::load($excelPath);
        
        $data = $this->buildPdfDataFromExcel($execution, $spreadsheet);
        
        $pdf = Pdf::loadView('pdfs.workflow-execution-excel', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $filename = "workflow_execution_{$execution->fileBatch->batch_code}.pdf";
        $path = storage_path("app/workflows/pdfs/{$filename}");
        
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $pdf->save($path);
        
        return $path;
    }
    
    /**
     * Build data array for PDF from Excel
     */
    protected function buildPdfDataFromExcel(WorkflowExecution $execution, $spreadsheet): array
    {
        $sheets = [];
        
        foreach ($spreadsheet->getAllSheets() as $index => $sheet) {
            $sheets[] = [
                'name' => $sheet->getTitle(),
                'data' => $this->extractSheetData($sheet),
            ];
        }
        
        return [
            'execution' => $execution,
            'batch' => $execution->fileBatch,
            'workflow_type' => $execution->fileBatch->workflowType,
            'client' => $execution->fileBatch->client,
            'branch' => $execution->fileBatch->branch,
            'user' => $execution->fileBatch->user,
            'sheets' => $sheets,
            'generated_at' => now(),
        ];
    }
    
    /**
     * Extract data from a worksheet
     */
    protected function extractSheetData(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        
        $data = [];
        
        for ($row = 1; $row <= min($highestRow, 100); $row++) {
            $rowData = [];
            $hasData = false;
            
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $value = $sheet->getCell($col . $row)->getCalculatedValue();
                
                if ($value !== null && $value !== '') {
                    $hasData = true;
                }
                
                $rowData[] = $value;
            }
            
            if ($hasData) {
                $data[] = $rowData;
            }
        }
        
        return $data;
    }
    
    /**
     * Download PDF directly
     */
    public function downloadExecutionReport(WorkflowExecution $execution): \Illuminate\Http\Response
    {
        if (!$execution->hasExcelResponse()) {
            throw new \Exception('No hay archivo Excel disponible para esta ejecución');
        }

        $excelPath = $execution->getExcelResponseFullPath();
        $spreadsheet = IOFactory::load($excelPath);
        
        $data = $this->buildPdfDataFromExcel($execution, $spreadsheet);
        
        $pdf = Pdf::loadView('pdfs.workflow-execution-excel', $data);
        $pdf->setPaper('a4', 'landscape');
        
        $filename = "workflow_execution_{$execution->fileBatch->batch_code}.pdf";
        
        return $pdf->download($filename);
    }
}
