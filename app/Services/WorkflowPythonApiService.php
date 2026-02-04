<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WorkflowPythonApiService
{
    protected string $apiUrl;
    protected string $conciliarUrl;
    protected string $procesarJsonUrl;
    protected string $arqueoUrl;
    protected int $timeout;
    protected bool $mockMode;

    /**
     * Map file identifiers from database to API field names for conciliation workflow
     */
    protected array $conciliationFileMapping = [
        'Turnos' => 'turnos',
        'Reporte_Ventas' => 'ventas',
        'Reporte_getnet' => 'getnet',
        'Prueba_MP' => 'mercado_pago',
        'Devoluciones' => 'devoluciones',
        'Caja_Adicion' => 'caja_adicion',
    ];

    public function __construct()
    {
        $this->apiUrl = config('services.workflow_python_api.url');
        $this->conciliarUrl = config('services.workflow_python_api.conciliar_url',
            str_replace('/procesar', '/conciliar', $this->apiUrl));
        $this->procesarJsonUrl = config('services.workflow_python_api.procesar_json_url',
            str_replace('/procesar', '/procesar-json', $this->apiUrl));
        $this->arqueoUrl = config('services.workflow_python_api.arqueo_url',
            str_replace('/procesar', '/arqueo', $this->apiUrl));
        $this->timeout = config('services.workflow_python_api.timeout', 120);
        $this->mockMode = config('services.workflow_python_api.mock_mode', true);
    }

    /**
     * Send files to Python API for processing
     *
     * @param array $files Array of UploadedFile instances keyed by file_identifier
     * @param string $workflowType Type of workflow (e.g., 'conciliacion')
     * @param int $executionId Execution ID for storage path
     * @return array ['success' => bool, 'excel_path' => string|null, 'error' => string|null]
     */
    public function processFiles(array $files, string $workflowType, int $executionId): array
    {
        if ($this->mockMode) {
            return $this->mockProcessFiles($files, $workflowType, $executionId);
        }

        try {
            // Determine if this is a conciliation workflow
            $isConciliation = str_contains(strtolower($workflowType), 'concilia') && !str_contains(strtolower($workflowType), 'arqueo');

            // Prepare multipart form data
            $multipart = [];
            foreach ($files as $fileIdentifier => $file) {
                // Map file identifier to API field name for conciliation workflows
                $fieldName = $fileIdentifier;
                if ($isConciliation && isset($this->conciliationFileMapping[$fileIdentifier])) {
                    $fieldName = $this->conciliationFileMapping[$fileIdentifier];
                }

                $multipart[] = [
                    'name' => $fieldName,
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ];

                Log::info("Archivo mapeado: {$fileIdentifier} -> {$fieldName}");
            }

            Log::info("Enviando archivos a Python API: {$this->apiUrl}", [
                'workflow_type' => $workflowType,
                'files_count' => count($multipart),
                'field_names' => array_column($multipart, 'name'),
            ]);

            // Send to Python API as multipart/form-data
            $response = Http::asMultipart()
                ->timeout($this->timeout)
                ->post($this->apiUrl, $multipart);

            if ($response->failed()) {
                Log::error("Python API falló: " . $response->body());
                return [
                    'success' => false,
                    'excel_path' => null,
                    'error' => 'Error al procesar archivos en el servidor Python: ' . $response->status(),
                ];
            }

            // Store the Excel response
            $excelPath = $this->storeExcelResponse($response->body(), $executionId);

            Log::info("Excel procesado y guardado en: $excelPath");

            return [
                'success' => true,
                'excel_path' => $excelPath,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en processFiles: " . $e->getMessage());
            return [
                'success' => false,
                'excel_path' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mock mode: Generate fake Excel response for testing
     */
    protected function mockProcessFiles(array $files, string $workflowType, int $executionId): array
    {
        try {
            // Determine if this is a conciliation workflow
            $isConciliation = str_contains(strtolower($workflowType), 'concilia');

            // Log the files that would be sent (with correct mapping)
            $mappedFiles = [];
            foreach ($files as $fileIdentifier => $file) {
                $fieldName = $fileIdentifier;
                if ($isConciliation && isset($this->conciliationFileMapping[$fileIdentifier])) {
                    $fieldName = $this->conciliationFileMapping[$fileIdentifier];
                }
                $mappedFiles[$fieldName] = $file->getClientOriginalName();
            }

            Log::info("MODO MOCK: Archivos que se enviarían a la API", [
                'workflow_type' => $workflowType,
                'is_conciliation' => $isConciliation,
                'mapped_files' => $mappedFiles,
            ]);

            Log::info("MODO MOCK: Generando Excel de respuesta simulado");

            // Create a mock Excel with 4 sheets matching arqueo.xlsm structure
            $spreadsheet = new Spreadsheet();
            
            // Sheet 1: ENVIAR SUCURSAL
            $sheet1 = $spreadsheet->getActiveSheet();
            $sheet1->setTitle('ENVIAR SUCURSAL');
            $sheet1->setCellValue('A1', 'REPORTE DE CONCILIACIÓN');
            $sheet1->setCellValue('A2', 'Cliente: MOCK CLIENT');
            $sheet1->setCellValue('A3', 'Fecha: ' . now()->format('d/m/Y H:i'));
            $sheet1->setCellValue('A5', 'Total Ventas:');
            $sheet1->setCellValue('B5', '$' . number_format(rand(10000, 50000), 2));

            // Sheet 2: ENVIAR EGRESOS
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('ENVIAR EGRESOS');
            $sheet2->setCellValue('B10', 'IMPORTE');
            $sheet2->setCellValue('C10', 'HORA');
            $sheet2->setCellValue('D10', 'DETALLE');
            
            for ($i = 11; $i <= 15; $i++) {
                $sheet2->setCellValue('B' . $i, '$' . number_format(rand(100, 1000), 2));
                $sheet2->setCellValue('C' . $i, rand(8, 20) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT));
                $sheet2->setCellValue('D' . $i, 'Egreso de prueba ' . ($i - 10));
            }

            // Sheet 3: ENVIAR NO CONCILIADOS
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('ENVIAR NO CONCILIADOS');
            $sheet3->setCellValue('B12', 'ID de Venta');
            $sheet3->setCellValue('C12', 'Hora');
            $sheet3->setCellValue('D12', 'Monto');
            $sheet3->setCellValue('F12', 'ID de Venta');
            $sheet3->setCellValue('G12', 'Hora');
            $sheet3->setCellValue('H12', 'Monto');
            
            for ($i = 13; $i <= 17; $i++) {
                $sheet3->setCellValue('B' . $i, 'V' . rand(1000, 9999));
                $sheet3->setCellValue('C' . $i, rand(8, 20) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT));
                $sheet3->setCellValue('D' . $i, '$' . number_format(rand(100, 500), 2));
            }

            // Sheet 4: ENVIAR ANULACIONES
            $sheet4 = $spreadsheet->createSheet();
            $sheet4->setTitle('ENVIAR ANULACIONES');
            $sheet4->setCellValue('B10', 'ID Comanda');
            $sheet4->setCellValue('C10', 'Camarero Mesa');
            $sheet4->setCellValue('D10', 'Producto');
            $sheet4->setCellValue('E10', 'Comentario');
            $sheet4->setCellValue('F10', 'Hora Anulación');
            $sheet4->setCellValue('G10', 'Precios');
            
            for ($i = 11; $i <= 15; $i++) {
                $sheet4->setCellValue('B' . $i, 'C' . rand(100, 999));
                $sheet4->setCellValue('C' . $i, 'Mesa ' . rand(1, 20));
                $sheet4->setCellValue('D' . $i, 'Producto ' . ($i - 10));
                $sheet4->setCellValue('E' . $i, 'Anulado por error');
                $sheet4->setCellValue('F' . $i, rand(8, 20) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT));
                $sheet4->setCellValue('G' . $i, '$' . number_format(rand(50, 300), 2));
            }

            // Save to temporary file
            $tempPath = storage_path('app/temp/mock_response_' . $executionId . '.xlsx');
            $directory = dirname($tempPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);

            // Read and store as if it came from API
            $excelContent = file_get_contents($tempPath);
            $excelPath = $this->storeExcelResponse($excelContent, $executionId);

            // Clean up temp file
            unlink($tempPath);

            Log::info("MODO MOCK: Excel simulado guardado en: $excelPath");

            return [
                'success' => true,
                'excel_path' => $excelPath,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en mockProcessFiles: " . $e->getMessage());
            return [
                'success' => false,
                'excel_path' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Store Excel response from API
     */
    protected function storeExcelResponse(string $content, int $executionId): string
    {
        $filename = "arqueo_resultado_{$executionId}_" . now()->format('YmdHis') . ".xlsx";
        $path = "workflows/executions/{$executionId}/{$filename}";

        Storage::disk('public')->put($path, $content);

        return $path;
    }

    /**
     * Validate file structure (column count, etc.)
     * This will be called before sending to API
     */
    public function validateFileStructure(array $files, string $workflowType): array
    {
        $errors = [];

        // TODO: Implement validation based on workflow type
        // For now, just check that files are Excel
        foreach ($files as $key => $file) {
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, ['xlsx', 'xls'])) {
                $errors[] = "El archivo '{$file->getClientOriginalName()}' no es un archivo Excel válido";
            }
        }

        return $errors;
    }

    /**
     * Send files to Python API /conciliar endpoint for JSON response
     *
     * @param array $files Array of UploadedFile instances keyed by file_identifier
     * @param int $executionId Execution ID for logging
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null]
     */
    public function processConciliar(array $files, int $executionId): array
    {
        if ($this->mockMode) {
            return $this->mockProcessConciliar($files, $executionId);
        }

        try {
            // Prepare multipart form data with mapped field names
            $multipart = [];
            foreach ($files as $fileIdentifier => $file) {
                $fieldName = $this->conciliationFileMapping[$fileIdentifier] ?? $fileIdentifier;

                $multipart[] = [
                    'name' => $fieldName,
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ];

                Log::info("Conciliar - Archivo mapeado: {$fileIdentifier} -> {$fieldName}");
            }

            Log::info("Enviando archivos a Python API (conciliar): {$this->conciliarUrl}", [
                'files_count' => count($multipart),
                'field_names' => array_column($multipart, 'name'),
            ]);

            // Send to Python API as multipart/form-data
            $response = Http::asMultipart()
                ->timeout($this->timeout)
                ->post($this->conciliarUrl, $multipart);

            if ($response->failed()) {
                Log::error("Python API /conciliar falló: " . $response->body());
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Error al procesar archivos en el servidor Python: ' . $response->status(),
                ];
            }

            $jsonResponse = $response->json();

            if (($jsonResponse['status'] ?? '') !== 'success') {
                Log::error("Python API /conciliar retornó error", ['response' => $jsonResponse]);
                return [
                    'success' => false,
                    'data' => null,
                    'error' => $jsonResponse['message'] ?? 'Error desconocido en la API',
                ];
            }

            Log::info("Conciliar - Respuesta exitosa recibida", [
                'execution_id' => $executionId,
                'sections' => array_keys($jsonResponse['data'] ?? []),
            ]);

            return [
                'success' => true,
                'data' => $jsonResponse,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en processConciliar: " . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mock mode for /conciliar endpoint: Load from conciliacion.json
     */
    protected function mockProcessConciliar(array $files, int $executionId): array
    {
        try {
            // Log the files that would be sent
            $mappedFiles = [];
            foreach ($files as $fileIdentifier => $file) {
                $fieldName = $this->conciliationFileMapping[$fileIdentifier] ?? $fileIdentifier;
                $mappedFiles[$fieldName] = $file->getClientOriginalName();
            }

            Log::info("MODO MOCK (conciliar): Archivos que se enviarían a la API", [
                'mapped_files' => $mappedFiles,
            ]);

            // Load mock data from conciliacion.json
            $mockFile = base_path('conciliacion.json');

            if (file_exists($mockFile)) {
                $content = file_get_contents($mockFile);
                $data = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info("MODO MOCK (conciliar): Datos cargados desde conciliacion.json", [
                        'execution_id' => $executionId,
                        'sections' => array_keys($data['data'] ?? []),
                    ]);

                    return [
                        'success' => true,
                        'data' => $data,
                        'error' => null,
                    ];
                }
            }

            // Fallback if file doesn't exist
            Log::warning("MODO MOCK (conciliar): conciliacion.json no encontrado, usando datos vacíos");
            return [
                'success' => true,
                'data' => [
                    'status' => 'success',
                    'message' => 'Mock response',
                    'data' => [
                        'turnos' => [],
                        'getnet' => [],
                        'mp' => [],
                        'sistema' => [],
                        'ventas_sistema' => [],
                        'caja_adicion' => [],
                        'mp_negativos' => [],
                        'comandas' => [],
                    ],
                ],
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en mockProcessConciliar: " . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send files to Python API /procesar-json endpoint for JSON response (Arqueo format)
     *
     * @param array $files Array of UploadedFile instances keyed by file_identifier
     * @param int $executionId Execution ID for logging
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null]
     */
    public function procesarJson(array $files, int $executionId): array
    {
        if ($this->mockMode) {
            return $this->mockProcesarJson($files, $executionId);
        }

        try {
            // Prepare multipart form data with mapped field names
            $multipart = [];
            foreach ($files as $fileIdentifier => $file) {
                $fieldName = $this->conciliationFileMapping[$fileIdentifier] ?? $fileIdentifier;

                $multipart[] = [
                    'name' => $fieldName,
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ];

                Log::info("ProcesarJson - Archivo mapeado: {$fileIdentifier} -> {$fieldName}");
            }

            Log::info("Enviando archivos a Python API (procesar-json): {$this->procesarJsonUrl}", [
                'files_count' => count($multipart),
                'field_names' => array_column($multipart, 'name'),
            ]);

            // Send to Python API as multipart/form-data
            $response = Http::asMultipart()
                ->timeout($this->timeout)
                ->post($this->procesarJsonUrl, $multipart);

            if ($response->failed()) {
                Log::error("Python API /procesar-json falló: " . $response->body());
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Error al procesar archivos en el servidor Python: ' . $response->status(),
                ];
            }

            $jsonResponse = $response->json();

            if (($jsonResponse['status'] ?? '') !== 'success') {
                Log::error("Python API /procesar-json retornó error", ['response' => $jsonResponse]);
                return [
                    'success' => false,
                    'data' => null,
                    'error' => $jsonResponse['message'] ?? 'Error desconocido en la API',
                ];
            }

            Log::info("ProcesarJson - Respuesta exitosa recibida", [
                'execution_id' => $executionId,
                'sections' => array_keys($jsonResponse['data'] ?? []),
            ]);

            return [
                'success' => true,
                'data' => $jsonResponse,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en procesarJson: " . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mock mode for /procesar-json endpoint: Load from arqueo_resultado_test.json
     */
    protected function mockProcesarJson(array $files, int $executionId): array
    {
        try {
            // Log the files that would be sent
            $mappedFiles = [];
            foreach ($files as $fileIdentifier => $file) {
                $fieldName = $this->conciliationFileMapping[$fileIdentifier] ?? $fileIdentifier;
                $mappedFiles[$fieldName] = $file->getClientOriginalName();
            }

            Log::info("MODO MOCK (procesar-json): Archivos que se enviarían a la API", [
                'mapped_files' => $mappedFiles,
            ]);

            // Load mock data from arqueo_resultado_test.json
            $mockFile = base_path('arqueo_resultado_test.json');

            if (file_exists($mockFile)) {
                $content = file_get_contents($mockFile);
                $data = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info("MODO MOCK (procesar-json): Datos cargados desde arqueo_resultado_test.json", [
                        'execution_id' => $executionId,
                        'sections' => array_keys($data['data'] ?? []),
                    ]);

                    return [
                        'success' => true,
                        'data' => $data,
                        'error' => null,
                    ];
                }
            }

            // Fallback if file doesn't exist
            Log::warning("MODO MOCK (procesar-json): arqueo_resultado_test.json no encontrado, usando datos vacíos");
            return [
                'success' => true,
                'data' => [
                    'status' => 'success',
                    'message' => 'Mock response',
                    'data' => [
                        'arqueo_por_turno' => [],
                        'getnet_conciliado' => [],
                        'mp_conciliado' => [],
                        'sistema_conciliado' => [],
                        'ventas_sistema' => [],
                        'turnos_procesados' => [],
                        'devoluciones' => [],
                        'caja_adicion' => [],
                        'mp_negativos' => [],
                    ],
                ],
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en mockProcesarJson: " . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send JSON data to Python API /arqueo endpoint for monthly arqueo generation
     *
     * @param array $data Array with conciliation data (turnos, getnet, mp, sistema, etc.)
     * @param int $executionId Execution ID for logging
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null]
     */
    public function processArqueo(array $data, int $executionId): array
    {
        if ($this->mockMode) {
            return $this->mockProcessArqueo($data, $executionId);
        }

        try {
            Log::info("Enviando datos a Python API (arqueo): {$this->arqueoUrl}", [
                'execution_id' => $executionId,
                'sections' => array_keys($data),
                'turnos_count' => count($data['turnos'] ?? []),
                'getnet_count' => count($data['getnet'] ?? []),
                'mp_count' => count($data['mp'] ?? []),
            ]);

            // Send JSON data to Python API
            $response = Http::timeout($this->timeout)
                ->post($this->arqueoUrl, $data);

            if ($response->failed()) {
                Log::error("Python API /arqueo falló: " . $response->body());
                return [
                    'success' => false,
                    'data' => null,
                    'error' => 'Error al procesar datos en el servidor Python: ' . $response->status(),
                ];
            }

            $jsonResponse = $response->json();

            if (($jsonResponse['status'] ?? '') !== 'success') {
                Log::error("Python API /arqueo retornó error", ['response' => $jsonResponse]);
                return [
                    'success' => false,
                    'data' => null,
                    'error' => $jsonResponse['message'] ?? 'Error desconocido en la API',
                ];
            }

            Log::info("Arqueo - Respuesta exitosa recibida", [
                'execution_id' => $executionId,
                'sections' => array_keys($jsonResponse['data'] ?? []),
                'arqueo_count' => count($jsonResponse['data']['arqueo_por_turno'] ?? []),
            ]);

            return [
                'success' => true,
                'data' => $jsonResponse,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en processArqueo: " . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mock mode for /arqueo endpoint: Load from arqueo.json
     */
    protected function mockProcessArqueo(array $data, int $executionId): array
    {
        try {
            Log::info("MODO MOCK (arqueo): Datos que se enviarían a la API", [
                'sections' => array_keys($data),
                'turnos_count' => count($data['turnos'] ?? []),
            ]);

            // Load mock data from arqueo.json
            $mockFile = base_path('arqueo.json');

            if (file_exists($mockFile)) {
                $content = file_get_contents($mockFile);
                $mockData = json_decode($content, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info("MODO MOCK (arqueo): Datos cargados desde arqueo.json", [
                        'execution_id' => $executionId,
                        'sections' => array_keys($mockData['data'] ?? []),
                    ]);

                    return [
                        'success' => true,
                        'data' => $mockData,
                        'error' => null,
                    ];
                }
            }

            // Fallback if file doesn't exist
            Log::warning("MODO MOCK (arqueo): arqueo.json no encontrado, usando datos vacíos");
            return [
                'success' => true,
                'data' => [
                    'status' => 'success',
                    'message' => 'Mock response',
                    'data' => [
                        'arqueo_por_turno' => [],
                    ],
                ],
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error("Error en mockProcessArqueo: " . $e->getMessage());
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
