<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ApiService;
use App\Models\Endpoint;

class EndpointController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, ApiService $service)
    {
        $preselectedMethod = $request->query('method');
        // Fetch existing endpoints to display in the list
        $endpoints = $service->endpoints()->latest()->get();
        // Fetch integrations (credentials) to allow testing
        $integrations = $service->clientCredentials()->with('client')->get();
        
        return view('programmer.endpoints.create', compact('service', 'endpoints', 'integrations', 'preselectedMethod'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ApiService $service)
    {
        $validated = $request->validate([
            'method' => 'required|in:GET,POST,PUT,DELETE,PATCH',
            'url' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $service->endpoints()->create($validated);

        // Redirect back to the same creation/management page
        return redirect()->route('programmer.services.endpoints.create', ['service' => $service->id])
            ->with('success', "Endpoint {$validated['method']} creado correctamente.");
    }

    public function executeTest(Request $request)
    {
        $request->validate([
            'endpoint_id' => 'required|exists:endpoints,id',
            'integration_id' => 'required|exists:client_credentials,id',
        ]);

        $endpoint = Endpoint::findOrFail($request->endpoint_id);
        $integration = \App\Models\ClientCredential::findOrFail($request->integration_id);
        $service = $endpoint->apiService;

        $url = rtrim($service->base_url, '/') . '/' . ltrim($endpoint->url, '/');
        $method = $endpoint->method;
        
        // Prepare Headers/Config (Basic logic for Mercado Pago and others)
        $headers = [];
        $params = [];

        $creds = $integration->credentials;
        
        // Auth Logic
        if(isset($creds['access_token'])) {
            $headers['Authorization'] = 'Bearer ' . $creds['access_token'];
        }
        
        // Todo: Handle body/query params based on endpoint definitions
        
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
                ->send($method, $url, $params);
            
            return response()->json([
                'success' => $response->successful(),
                'data' => $response->json(),
                'status' => $response->status(),
                'message' => $response->successful() ? 'Petición exitosa' : 'Error HTTP ' . $response->status(),
                'details' => $response->json() // In case of error, show body
            ]);
        } catch (\Exception $e) {
             return response()->json([
                'success' => false,
                'message' => 'Error de Sistema: ' . $e->getMessage(),
                'details' => []
            ]);
        }
    }
}
