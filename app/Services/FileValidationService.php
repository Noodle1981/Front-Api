<?php

namespace App\Services;

use App\Models\WorkflowFileBatch;
use App\Models\WorkflowFileDefinition;
use App\Models\WorkflowUploadedFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
     * Get headers from an Excel file using PhpSpreadsheet directly
     */
    protected function getFileHeaders(WorkflowUploadedFile $uploadedFile): array
    {
        $filePath = $uploadedFile->getFullPath();

        return $this->readHeadersFromFile($filePath);
    }

    /**
     * Read headers from first row of Excel file using PhpSpreadsheet
     */
    protected function readHeadersFromFile(string $filePath): array
    {
        $headers = [];

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $sheet->getCellByColumnAndRow($col, 1)->getValue();
                if ($cellValue !== null && $cellValue !== '') {
                    $headers[] = (string) $cellValue;
                }
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

        } catch (\Exception $e) {
            Log::error('Error reading Excel headers', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $headers;
    }

    /**
     * Match an uploaded file to its definition based on column structure
     * Uses PhpSpreadsheet directly for better compatibility with shared hosting
     */
    public function matchFileDefinition(UploadedFile $file, Collection $definitions): ?WorkflowFileDefinition
    {
        $filePath = $file->getRealPath();
        $fileName = $file->getClientOriginalName();

        Log::info('Attempting to match file definition', [
            'filename' => $fileName,
            'path' => $filePath,
            'exists' => file_exists($filePath),
            'readable' => is_readable($filePath),
            'size' => $file->getSize(),
        ]);

        // Verify file exists and is readable
        if (!file_exists($filePath) || !is_readable($filePath)) {
            Log::error('File not accessible for matching', [
                'filename' => $fileName,
                'path' => $filePath,
            ]);
            return null;
        }

        try {
            // Read headers using PhpSpreadsheet directly
            $headers = $this->readHeadersFromFile($filePath);

            Log::info('Headers extracted from file', [
                'filename' => $fileName,
                'headers_count' => count($headers),
                'headers' => array_slice($headers, 0, 10), // Log first 10 headers
            ]);

            if (empty($headers)) {
                Log::warning('No headers found in file', ['filename' => $fileName]);
                return null;
            }

            // Normalize headers
            $normalizedHeaders = $this->normalizeColumnNames($headers);

            Log::info('Normalized headers for matching', [
                'filename' => $fileName,
                'normalized_headers' => $normalizedHeaders,
            ]);

            // Try to match with each definition
            $matchAttempts = [];
            foreach ($definitions as $definition) {
                $requiredColumns = $definition->requiredColumns()
                    ->where('is_required', true)
                    ->pluck('column_name')
                    ->toArray();

                $normalizedRequired = $this->normalizeColumnNames($requiredColumns);

                // Check if all required columns are present
                $missingColumns = array_diff($normalizedRequired, $normalizedHeaders);

                // Log each attempt for debugging
                $matchAttempts[] = [
                    'definition' => $definition->display_name,
                    'required' => $normalizedRequired,
                    'missing' => array_values($missingColumns),
                    'matched' => empty($missingColumns),
                ];

                if (empty($missingColumns)) {
                    Log::info('File matched to definition', [
                        'filename' => $fileName,
                        'definition' => $definition->display_name,
                    ]);
                    return $definition;
                }
            }

            // Log detailed mismatch information
            Log::warning('No definition matched for file - detailed analysis', [
                'filename' => $fileName,
                'file_headers' => $headers,
                'normalized_headers' => $normalizedHeaders,
                'match_attempts' => $matchAttempts,
            ]);

        } catch (\Exception $e) {
            Log::error('Exception matching file definition', [
                'filename' => $fileName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
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
     * Handles accents, special characters, and encoding differences
     */
    protected function normalizeColumnName(string $name): string
    {
        // Trim whitespace
        $name = trim($name);

        // Convert to lowercase
        $name = mb_strtolower($name, 'UTF-8');

        // Remove accents/diacritics
        $name = $this->removeAccents($name);

        // Replace spaces and special chars with underscore
        $name = preg_replace('/[\s\-\.]+/', '_', $name);

        // Remove any remaining non-alphanumeric characters except underscore
        $name = preg_replace('/[^a-z0-9_]/', '', $name);

        // Remove multiple consecutive underscores
        $name = preg_replace('/_+/', '_', $name);

        // Trim underscores from start/end
        $name = trim($name, '_');

        return $name;
    }

    /**
     * Remove accents from string (á -> a, ñ -> n, etc.)
     */
    protected function removeAccents(string $string): string
    {
        $accents = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
            'Á' => 'a', 'À' => 'a', 'Ä' => 'a', 'Â' => 'a', 'Ã' => 'a',
            'É' => 'e', 'È' => 'e', 'Ë' => 'e', 'Ê' => 'e',
            'Í' => 'i', 'Ì' => 'i', 'Ï' => 'i', 'Î' => 'i',
            'Ó' => 'o', 'Ò' => 'o', 'Ö' => 'o', 'Ô' => 'o', 'Õ' => 'o',
            'Ú' => 'u', 'Ù' => 'u', 'Ü' => 'u', 'Û' => 'u',
            'Ñ' => 'n', 'Ç' => 'c',
        ];

        return strtr($string, $accents);
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
