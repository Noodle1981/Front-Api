<?php

namespace App\Services;

use App\Models\WorkflowFileBatch;
use App\Models\WorkflowExecution;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkflowExecutionService
{
    protected WorkflowJsonGeneratorService $jsonGenerator;
    protected ConciliationDataService $conciliationService;

    public function __construct(
        WorkflowJsonGeneratorService $jsonGenerator,
        ConciliationDataService $conciliationService
    ) {
        $this->jsonGenerator = $jsonGenerator;
        $this->conciliationService = $conciliationService;
    }
    
    /**
     * Execute a workflow batch
     */
    public function execute(WorkflowFileBatch $batch): WorkflowExecution
    {
        // Update batch status to executing
        $this->updateBatchStatus($batch, 'executing');
        
        try {
            // Generate JSON from batch
            $jsonData = $this->jsonGenerator->generateFromBatch($batch);
            
            // Record start time
            $startTime = microtime(true);
            
            // Call API (mock or real)
            $useMock = config('workflows.python_api.use_mock', true);
            
            if ($useMock) {
                $response = $this->mockApiResponse($jsonData);
            } else {
                $response = $this->callExternalApi($jsonData);
            }
            
            // Calculate execution time
            $executionTime = (int) ((microtime(true) - $startTime) * 1000);
            
            // Save execution
            $execution = $this->saveExecution($batch, $jsonData, $response, $executionTime);

            // Process conciliation data if applicable
            if ($this->isConciliationWorkflow($batch)) {
                $this->processConciliationResponse($execution, $response);
            }

            // Update batch status based on response
            $status = ($response['status'] ?? 'failed') === 'success' ? 'completed' : 'failed';
            $this->updateBatchStatus($batch, $status);

            return $execution;
            
        } catch (\Exception $e) {
            // Update batch status to failed
            $this->updateBatchStatus($batch, 'failed');
            
            // Log error
            Log::error('Workflow execution failed', [
                'batch_id' => $batch->id,
                'error' => $e->getMessage(),
            ]);
            
            // Create failed execution record
            return $this->saveExecution(
                $batch,
                $jsonData ?? [],
                [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ],
                0
            );
        }
    }
    
    /**
     * Generate a mock API response for development
     */
    public function mockApiResponse(array $jsonData): array
    {
        // Simulate processing delay
        usleep(500000); // 500ms
        
        // Count total records from all files
        $totalRecords = 0;
        foreach ($jsonData['Data'] ?? [] as $fileData) {
            $totalRecords += count($fileData);
        }
        
        // Simulate some validation errors (10% of records)
        $invalidRecords = (int) ($totalRecords * 0.1);
        $validRecords = $totalRecords - $invalidRecords;
        
        return [
            'status' => 'success',
            'message' => 'Workflow ejecutado correctamente (MOCK)',
            'results' => [
                'total_records' => $totalRecords,
                'valid_records' => $validRecords,
                'invalid_records' => $invalidRecords,
                'errors' => $invalidRecords > 0 ? [
                    'Se encontraron ' . $invalidRecords . ' registros con errores de validación',
                ] : [],
                'warnings' => [],
            ],
            'execution_time_ms' => rand(500, 2000),
            'mock' => true,
        ];
    }
    
    /**
     * Call the external Python API
     */
    public function callExternalApi(array $jsonData): array
    {
        $url = config('workflows.python_api.url');
        $timeout = config('workflows.python_api.timeout', 60);
        
        try {
            $response = Http::timeout($timeout)
                ->post($url, $jsonData);
            
            if ($response->successful()) {
                return $response->json();
            } else {
                throw new \Exception("API returned status {$response->status()}: {$response->body()}");
            }
            
        } catch (\Exception $e) {
            throw new \Exception("Error calling Python API: {$e->getMessage()}");
        }
    }
    
    /**
     * Save workflow execution to database
     */
    public function saveExecution(
        WorkflowFileBatch $batch,
        array $jsonSent,
        array $jsonResponse,
        int $executionTime
    ): WorkflowExecution {
        return WorkflowExecution::create([
            'workflow_id' => null, // Legacy field, can be null
            'workflow_file_batch_id' => $batch->id,
            'status' => $jsonResponse['status'] ?? 'failed',
            'logs_json' => $jsonResponse['results'] ?? [],
            'json_sent' => $jsonSent,
            'json_response' => $jsonResponse,
            'execution_time_ms' => $executionTime,
            'started_at' => now(),
            'completed_at' => now(),
        ]);
    }
    
    /**
     * Update batch status
     */
    public function updateBatchStatus(WorkflowFileBatch $batch, string $status): void
    {
        $batch->update(['status' => $status]);

        if ($status === 'completed' || $status === 'failed') {
            $batch->update(['validated_at' => now()]);
        }
    }

    /**
     * Check if the workflow is a conciliation workflow
     */
    protected function isConciliationWorkflow(WorkflowFileBatch $batch): bool
    {
        // Check by workflow type name
        $workflowType = $batch->workflowType;
        if ($workflowType && str_contains(strtolower($workflowType->name), 'concilia')) {
            return true;
        }

        // Check by configured type ID
        $conciliationTypeId = config('workflows.conciliation_type_id');
        if ($conciliationTypeId && $batch->workflow_type_id === $conciliationTypeId) {
            return true;
        }

        return false;
    }

    /**
     * Process and save conciliation response data
     */
    protected function processConciliationResponse(WorkflowExecution $execution, array $response): void
    {
        // Validate response status
        if (!$this->conciliationService->validateResponse($response)) {
            Log::warning('Conciliation response invalid or failed', [
                'execution_id' => $execution->id,
                'status' => $response['status'] ?? 'unknown',
                'message' => $response['message'] ?? 'No message',
            ]);
            return;
        }

        // Save conciliation data (with duplicate filtering via upsert)
        try {
            $stats = $this->conciliationService->processAndSave($execution, $response);

            Log::info('Conciliation data saved successfully', [
                'execution_id' => $execution->id,
                'total_processed' => $stats['total_processed'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save conciliation data', [
                'execution_id' => $execution->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - JSON data is already saved as backup in json_response
        }
    }
}
