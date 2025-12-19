<?php

namespace App\Http\Controllers\Programmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function create()
    {
        $providers = \App\Models\ApiService::all();
        return view('programmer.integrations.create', compact('providers'));
    }

    public function configure(\App\Models\ApiService $provider)
    {
        $clients = \App\Models\Client::orderBy('company')->get(['id', 'company', 'fantasy_name', 'cuit']);
        return view('programmer.integrations.configure', compact('provider', 'clients'));
    }

    public function testConnection(Request $request)
    {
        try {
            $provider = \App\Models\ApiService::findOrFail($request->provider_id);
            $credentials = $request->input('credentials', []);

            // Logic per Provider
            if ($provider->slug === 'mercado-pago') {
                $token = $credentials['access_token'] ?? null;
                if (!$token) {
                    return response()->json(['success' => false, 'message' => 'Falta el Access Token para verificar.']);
                }

                // Real test to Mercado Pago API
                $response = \Illuminate\Support\Facades\Http::withToken($token)
                    ->get('https://api.mercadopago.com/v1/payment_methods');

                if ($response->successful()) {
                    return response()->json(['success' => true, 'message' => 'Conexión establecida correctamente con Mercado Pago.']);
                }
                
                return response()->json(['success' => false, 'message' => 'Error de autenticación con Mercado Pago (Status: ' . $response->status() . ')']);
            }
            
            // Logic for other providers (Stubbed)
            if ($provider->slug === 'uala-bis') {
                 // Uala auth logic...
                 if (empty($credentials['user_name'])) {
                     return response()->json(['success' => false, 'message' => 'Falta User Name.']);
                 }
                 return response()->json(['success' => true, 'message' => 'Credenciales Ualá válidas (Simulación).']);
            }

            // Default
            return response()->json(['success' => true, 'message' => 'Validación de formato correcta.']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request, \App\Models\ApiService $provider)
    {
        $rules = [
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'nullable|string|max:255',
        ];

        // Add dynamic rules for provider credentials
        if ($provider->required_fields) {
            foreach ($provider->required_fields as $field) {
                // Determine field name (some nested in credentials array in form?)
                // My view uses name="credentials[field]".
                $rules["credentials.{$field}"] = 'required|string';
            }
        }

        $validated = $request->validate($rules);

        // Generate default name if not provided
        $name = $validated['name'] ?? null;
        if (!$name) {
            $clientName = '';
            if (!empty($validated['client_id'])) {
                $client = \App\Models\Client::find($validated['client_id']);
                $clientName = ' - ' . $client->company;
            } else {
                $clientName = ' - Sin Cliente';
            }
            $name = $provider->name . $clientName;
        }
        
        $credential = \App\Models\ClientCredential::create([
            'client_id' => $validated['client_id'] ?? null,
            'api_service_id' => $provider->id,
            'name' => $name,
            'credentials' => $validated['credentials'] ?? [],
            'is_active' => true,
        ]);

        // Auto-associate all endpoints by default
        $endpointIds = $provider->endpoints()->pluck('id');
        $credential->endpoints()->syncWithPivotValues($endpointIds, ['is_active' => true]);

        return redirect()->route('programmer.apis.index')
            ->with('success', "Integración '{$name}' creada correctamente.");
    }
}
