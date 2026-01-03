<?php

namespace App\Services;

use App\Models\WorkflowFileBatch;
use App\Models\WorkflowUploadedFile;
use Maatwebsite\Excel\Facades\Excel;

class WorkflowJsonGeneratorService
{
    /**
     * Generate JSON structure from a workflow batch
     */
    public function generateFromBatch(WorkflowFileBatch $batch): array
    {
        // Read all files data
        $filesData = [];
        
        foreach ($batch->uploadedFiles as $uploadedFile) {
            $fileIdentifier = $uploadedFile->fileDefinition->file_identifier;
            $filesData[$fileIdentifier] = $this->readExcelFile($uploadedFile);
        }
        
        // Build complete JSON structure
        return $this->buildJsonStructure($batch, $filesData);
    }
    
    /**
     * Read data from an Excel file
     */
    public function readExcelFile(WorkflowUploadedFile $file): array
    {
        $filePath = $file->getFullPath();
        
        try {
            // Read Excel file to array
            $data = Excel::toArray(new \stdClass(), $filePath);
            
            // Get first sheet data
            $sheetData = $data[0] ?? [];
            
            // Remove header row and return data
            if (count($sheetData) > 0) {
                $headers = array_shift($sheetData);
                
                // Convert to associative array
                return array_map(function ($row) use ($headers) {
                    $result = [];
                    foreach ($headers as $index => $header) {
                        $result[$header] = $row[$index] ?? null;
                    }
                    return $result;
                }, $sheetData);
            }
            
            return [];
            
        } catch (\Exception $e) {
            throw new \Exception("Error al leer archivo {$file->original_filename}: {$e->getMessage()}");
        }
    }
    
    /**
     * Build the complete JSON structure
     */
    public function buildJsonStructure(WorkflowFileBatch $batch, array $filesData): array
    {
        return [
            'metadata' => [
                'batch_code' => $batch->batch_code,
                'workflow_type' => $batch->workflowType->name,
                'workflow_type_id' => $batch->workflow_type_id,
                'client_id' => $batch->client_id,
                'client_name' => $batch->client->name ?? null,
                'branch_id' => $batch->branch_id,
                'branch_name' => $batch->branch->name ?? null,
                'uploaded_at' => $batch->uploaded_at->toIso8601String(),
                'uploaded_by' => $batch->user->name ?? null,
                'total_files' => count($filesData),
            ],
            'Data' => $filesData,
        ];
    }
    
    /**
     * Generate JSON and save to file (optional)
     */
    public function generateAndSaveToFile(WorkflowFileBatch $batch, string $path = null): string
    {
        $json = $this->generateFromBatch($batch);
        
        // Default path if not provided
        if (!$path) {
            $path = storage_path("app/workflows/json/{$batch->batch_code}.json");
        }
        
        // Ensure directory exists
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Save JSON to file
        file_put_contents($path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        return $path;
    }
}
