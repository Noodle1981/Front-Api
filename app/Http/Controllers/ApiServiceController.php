<?php

namespace App\Http\Controllers;

use App\Models\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiServiceController extends Controller
{
    public function index()
    {
        // Programmers see valid Integrations (ClientCredentials)
        // We call them "APIs" in the UI for simplicity
        $apis = \App\Models\ClientCredential::with(['client', 'apiService'])
                    ->latest()
                    ->paginate(10);
        
        return view('programmer.apis.index', compact('apis'));
    }

    public function create(Request $request)
    {
        // If user explicitly wants to define a new Custom API Service from scratch
        if ($request->query('mode') === 'manual') {
            $clients = \App\Models\Client::orderBy('company')->get(['id', 'company', 'fantasy_name', 'cuit']);
            return view('programmer.apis.form', compact('clients')); 
        }

        // Default: Show the Provider/Template Catalog
        $providers = ApiService::withCount('endpoints')->get();
        return view('programmer.apis.create', compact('providers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:api_services',
            'base_url' => 'required|url',
            'required_fields' => 'nullable|array', 
            'client_id' => 'nullable|exists:clients,id', // Validate client_id if present
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $apiService = ApiService::create($validated);

        $message = 'API creada correctamente.';

        // Handle Quick Assign to Client
        if (!empty($validated['client_id'])) {
            \App\Models\ClientCredential::create([
                'client_id' => $validated['client_id'],
                'api_service_id' => $apiService->id,
                'credentials' => [], // Empty credentials initially
                'is_active' => true,
            ]);
            $client = \App\Models\Client::find($validated['client_id']);
            $message .= " Y vinculada al cliente {$client->company}.";
        }

        return redirect()->route('programmer.apis.index')->with('success', $message);
    }

    public function edit($id)
    {
        $credential = \App\Models\ClientCredential::with(['apiService', 'client'])->findOrFail($id);
        $provider = $credential->apiService;
        $clients = \App\Models\Client::orderBy('company')->get(['id', 'company', 'fantasy_name', 'cuit']);
        
        return view('programmer.apis.edit', compact('credential', 'provider', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $credential = \App\Models\ClientCredential::findOrFail($id);
        $provider = $credential->apiService;

        $rules = [
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'nullable|string|max:255',
        ];

        if ($provider->required_fields) {
            foreach ($provider->required_fields as $field) {
                $rules["credentials.{$field}"] = 'required|string';
            }
        }

        $validated = $request->validate($rules);

        $credential->update([
            'client_id' => $validated['client_id'] ?? null,
            'name' => $validated['name'] ?? $credential->name,
            'credentials' => $validated['credentials'] ?? [],
        ]);

        return redirect()->route('programmer.apis.index')->with('success', 'Integración actualizada correctamente.');
    }

    public function destroy($id)
    {
        $credential = \App\Models\ClientCredential::findOrFail($id);
        $credential->delete();
        return redirect()->route('programmer.apis.index')->with('success', 'Integración eliminada correctamente.');
    }
}
