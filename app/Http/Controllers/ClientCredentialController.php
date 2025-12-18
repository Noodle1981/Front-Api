<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientCredential;
use App\Models\ApiService;
use Illuminate\Http\Request;

class ClientCredentialController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Client $client)
    {
        if (!$client->belongsToUser()) {
            abort(403);
        }

        $request->validate([
            'api_service_id' => 'required|exists:api_services,id',
            'credentials' => 'required|array',
            'execution_frequency' => 'nullable|string',
            'alert_email' => 'nullable|email',
        ]);

        $apiService = ApiService::findOrFail($request->api_service_id);

        // Validar que todos los campos requeridos estén presentes
        foreach ($apiService->required_fields as $field) {
            if (empty($request->credentials[$field])) {
                return back()->with('error', "El campo '$field' es obligatorio para " . $apiService->name . ".");
            }
        }

        // Crear credencial
        $client->credentials()->create([
            'api_service_id' => $apiService->id,
            'credentials' => $request->credentials,
            'is_active' => true,
            'execution_frequency' => $request->execution_frequency,
            'alert_email' => $request->alert_email,
        ]);

        return back()->with('success', 'Credenciales guardadas y encriptadas correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClientCredential $credential)
    {
        // Verificar que el usuario tenga permiso si es necesario
        $credential->delete();
        return back()->with('success', 'Credencial eliminada.');
    }
}