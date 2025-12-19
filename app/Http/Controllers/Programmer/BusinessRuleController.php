<?php

namespace App\Http\Controllers\Programmer;

use App\Http\Controllers\Controller;
use App\Models\BusinessRule;
use App\Models\Client;
use App\Models\ApiService;
use App\Models\Endpoint;
use Illuminate\Http\Request;

class BusinessRuleController extends Controller
{
    /**
     * Vista principal: Dashboard de Reglas de Negocio ETL
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        
        $query = BusinessRule::with(['client', 'apiService', 'endpoint', 'creator']);
        
        if ($type && in_array($type, ['extraction', 'transformation', 'visualization'])) {
            $query->where('type', $type);
        }
        
        $rules = $query->latest()->paginate(15);
        
        // Estadísticas
        $stats = [
            'total' => BusinessRule::count(),
            'extraction' => BusinessRule::where('type', 'extraction')->count(),
            'transformation' => BusinessRule::where('type', 'transformation')->count(),
            'visualization' => BusinessRule::where('type', 'visualization')->count(),
        ];
        
        return view('programmer.business-rules.index', compact('rules', 'stats', 'type'));
    }

    /**
     * Formulario de creación (Workflow Builder)
     */
    public function create()
    {
        $clients = Client::orderBy('company')->get(['id', 'company', 'fantasy_name']);
        $apiServices = ApiService::all();
        
        return view('programmer.business-rules.create', compact('clients', 'apiServices'));
    }

    /**
     * Guardar nueva regla
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:extraction,transformation,visualization',
            'status' => 'required|in:active,inactive',
            'client_id' => 'nullable|exists:clients,id',
            'api_service_id' => 'nullable|exists:api_services,id',
            'endpoint_id' => 'nullable|exists:endpoints,id',
            'python_code' => 'required|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['input_schema'] = $request->input('input_schema');
        $validated['output_schema'] = $request->input('output_schema');
        
        BusinessRule::create($validated);

        return redirect()
            ->route('programmer.business-rules.index')
            ->with('success', 'Regla de negocio creada exitosamente');
    }

    /**
     * Editar regla existente
     */
    public function edit(BusinessRule $rule)
    {
        $clients = Client::orderBy('company')->get(['id', 'company', 'fantasy_name']);
        $apiServices = ApiService::all();
        $endpoints = [];
        
        if ($rule->api_service_id) {
            $endpoints = Endpoint::where('api_service_id', $rule->api_service_id)->get();
        }
        
        return view('programmer.business-rules.edit', compact('rule', 'clients', 'apiServices', 'endpoints'));
    }

    /**
     * Actualizar regla
     */
    public function update(Request $request, BusinessRule $rule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:extraction,transformation,visualization',
            'status' => 'required|in:active,inactive',
            'client_id' => 'nullable|exists:clients,id',
            'api_service_id' => 'nullable|exists:api_services,id',
            'endpoint_id' => 'nullable|exists:endpoints,id',
            'python_code' => 'required|string',
        ]);

        $validated['input_schema'] = $request->input('input_schema');
        $validated['output_schema'] = $request->input('output_schema');
        
        $rule->update($validated);

        return redirect()
            ->route('programmer.business-rules.index')
            ->with('success', 'Regla de negocio actualizada exitosamente');
    }

    /**
     * Eliminar regla
     */
    public function destroy(BusinessRule $rule)
    {
        $rule->delete();

        return redirect()
            ->route('programmer.business-rules.index')
            ->with('success', 'Regla de negocio eliminada exitosamente');
    }

    /**
     * API: Obtener endpoints de un servicio API
     */
    public function getEndpoints($apiServiceId)
    {
        $endpoints = Endpoint::where('api_service_id', $apiServiceId)
            ->get(['id', 'name', 'method', 'path']);
        
        return response()->json($endpoints);
    }

    /**
     * API: Ejecutar prueba (devuelve datos mock para Pyodide)
     */
    public function executeTest(Request $request, BusinessRule $rule)
    {
        // Si hay un endpoint asociado, devolver datos de ejemplo
        if ($rule->endpoint) {
            $mockData = [
                'users' => [
                    ['id' => 1, 'name' => 'Juan Pérez', 'email' => 'juan@example.com'],
                    ['id' => 2, 'name' => 'María García', 'email' => 'maria@example.com'],
                    ['id' => 3, 'name' => 'Carlos López', 'email' => 'carlos@example.com'],
                ],
                'metadata' => [
                    'total' => 3,
                    'page' => 1,
                    'timestamp' => now()->toIso8601String(),
                ],
            ];
            
            // Incrementar contador de ejecuciones
            $rule->incrementExecutionCount();
            
            return response()->json([
                'success' => true,
                'data' => $mockData,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'No hay endpoint asociado a esta regla',
        ], 400);
    }

    /**
     * API: Obtener datos de entrada para el workflow builder
     */
    public function getInputData(Request $request)
    {
        $endpointId = $request->get('endpoint_id');
        
        if (!$endpointId) {
            return response()->json([
                'success' => false,
                'message' => 'Selecciona un endpoint'
            ]);
        }
        
        $endpoint = Endpoint::find($endpointId);
        
        if (!$endpoint) {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint no encontrado'
            ]);
        }
        
        // Datos de ejemplo basados en el endpoint
        $sampleData = [
            'endpoint' => [
                'name' => $endpoint->name,
                'method' => $endpoint->method,
                'path' => $endpoint->path,
            ],
            'sample_response' => [
                'data' => [
                    ['id' => 1, 'name' => 'Item 1', 'value' => 100],
                    ['id' => 2, 'name' => 'Item 2', 'value' => 200],
                    ['id' => 3, 'name' => 'Item 3', 'value' => 300],
                ],
                'status' => 'success',
                'timestamp' => now()->toIso8601String(),
            ]
        ];
        
        return response()->json([
            'success' => true,
            'data' => $sampleData
        ]);
    }
}
