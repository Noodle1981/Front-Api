<?php

namespace App\Services;

use App\Models\WorkflowFileBatch;
use App\Models\WorkflowFileDefinition;
use App\Models\WorkflowUploadedFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class FileValidationService
{
    /**
     * Validate a complete batch of uploaded files
     */
    public function validateBatch(WorkflowFileBatch $batch): array
    {
        $errors = [];
        
        // Get workflow type and expected files
        $workflowType = $batch->workflowType;
        $expectedCount = $workflowType->expected_files_count;
        $uploadedFiles = $batch->uploadedFiles;
        
        // Validate file count
        if ($uploadedFiles->count() !== $expectedCount) {
            $errors[] = [
                'type' => 'file_count',
                'message' => "Se esperaban {$expectedCount} archivos, pero se cargaron {$uploadedFiles->count()}",
            ];
        }
        
        // Get file definitions for this workflow
        $fileDefinitions = $workflowType->fileDefinitions;
        
        // Check for duplicates
        $duplicates = $this->detectDuplicates($uploadedFiles);
        if (!empty($duplicates)) {
            $errors[] = [
                'type' => 'duplicates',
                'message' => 'Se encontraron archivos duplicados',
                'files' => $duplicates,
            ];
        }
        
        // Check for missing files
        $missing = $this->detectMissing($batch);
        if (!empty($missing)) {
            $errors[] = [
                'type' => 'missing',
                'message' => 'Faltan archivos requeridos',
                'files' => $missing,
            ];
        }
        
        // Validate each uploaded file
        foreach ($uploadedFiles as $uploadedFile) {
            $fileErrors = $this->validateSingleFile($uploadedFile);
            if (!empty($fileErrors)) {
                $errors[] = [
                    'type' => 'file_validation',
                    'file' => $uploadedFile->original_filename,
                    'errors' => $fileErrors,
                ];
            }
        }
        
        return $errors;
    }
    
    /**
     * Validate a single uploaded file
     */
    protected function validateSingleFile(WorkflowUploadedFile $uploadedFile): array
    {
        $errors = [];
        
        try {
            // Read file headers
            $headers = $this->getFileHeaders($uploadedFile);
            
            // Normalize headers
            $normalizedHeaders = $this->normalizeColumnNames($headers);
            
            // Check required columns
            $columnErrors = $this->checkRequiredColumns(
                $normalizedHeaders,
                $uploadedFile->fileDefinition
            );
            
            if (!empty($columnErrors)) {
                $errors = array_merge($errors, $columnErrors);
            }
            
        } catch (\Exception $e) {
            $errors[] = "Error al leer el archivo: {$e->getMessage()}";
        }
        
        return $errors;
    }
    
    /**
     * Get headers from an Excel file
     */
    protected function getFileHeaders(WorkflowUploadedFile $uploadedFile): array
    {
        $filePath = $uploadedFile->getFullPath();
        
        // Read only the first row (headers)
        $import = new HeadingRowImport();
        $data = Excel::toArray($import, $filePath);
        
        return $data[0][0] ?? [];
    }
    
    /**
     * Match an uploaded file to its definition based on column structure
     */
    public function matchFileDefinition(UploadedFile $file, Collection $definitions): ?WorkflowFileDefinition
    {
        try {
            // Get file headers
            $import = new HeadingRowImport();
            $data = Excel::toArray($import, $file->getRealPath());
            $headers = $data[0][0] ?? [];
            
            // Normalize headers
            $normalizedHeaders = $this->normalizeColumnNames($headers);
            
            // Try to match with each definition
            foreach ($definitions as $definition) {
                $requiredColumns = $definition->requiredColumns()
                    ->where('is_required', true)
                    ->pluck('column_name')
                    ->toArray();
                
                $normalizedRequired = $this->normalizeColumnNames($requiredColumns);
                
                // Check if all required columns are present
                $missingColumns = array_diff($normalizedRequired, $normalizedHeaders);
                
                if (empty($missingColumns)) {
                    return $definition;
                }
            }
            
        } catch (\Exception $e) {
            // If we can't read the file, return null
            return null;
        }
        
        return null;
    }
    
    /**
     * Check if all required columns are present
     */
    public function checkRequiredColumns(array $headers, WorkflowFileDefinition $definition): array
    {
        $errors = [];
        
        $requiredColumns = $definition->requiredColumns()
            ->where('is_required', true)
            ->get();
        
        foreach ($requiredColumns as $column) {
            $normalizedColumnName = $this->normalizeColumnName($column->column_name);
            
            if (!in_array($normalizedColumnName, $headers)) {
                $errors[] = "Falta la columna requerida: {$column->column_name}";
            }
        }
        
        return $errors;
    }
    
    /**
     * Normalize column names (lowercase, trim, remove extra spaces)
     */
    public function normalizeColumnNames(array $headers): array
    {
        return array_map(function ($header) {
            return $this->normalizeColumnName($header);
        }, $headers);
    }
    
    /**
     * Normalize a single column name
     */
    protected function normalizeColumnName(string $name): string
    {
        return strtolower(trim(preg_replace('/\s+/', '_', $name)));
    }
    
    /**
     * Detect duplicate file types in the batch
     */
    public function detectDuplicates(Collection $files): array
    {
        $duplicates = [];
        $seen = [];
        
        foreach ($files as $file) {
            $definitionId = $file->workflow_file_definition_id;
            
            if (isset($seen[$definitionId])) {
                $duplicates[] = [
                    'file_type' => $file->fileDefinition->display_name,
                    'files' => [
                        $seen[$definitionId],
                        $file->original_filename,
                    ],
                ];
            } else {
                $seen[$definitionId] = $file->original_filename;
            }
        }
        
        return $duplicates;
    }
    
    /**
     * Detect missing required files in the batch
     */
    public function detectMissing(WorkflowFileBatch $batch): array
    {
        $missing = [];
        
        $requiredDefinitions = $batch->workflowType->fileDefinitions()
            ->where('is_required', true)
            ->get();
        
        $uploadedDefinitionIds = $batch->uploadedFiles()
            ->pluck('workflow_file_definition_id')
            ->toArray();
        
        foreach ($requiredDefinitions as $definition) {
            if (!in_array($definition->id, $uploadedDefinitionIds)) {
                $missing[] = [
                    'file_type' => $definition->display_name,
                    'identifier' => $definition->file_identifier,
                ];
            }
        }
        
        return $missing;
    }
}
