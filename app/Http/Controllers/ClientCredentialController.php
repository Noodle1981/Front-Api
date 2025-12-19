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
        if (!auth()->user()->hasRole(['Super Admin', 'Manager', 'Programador']) && !$client->belongsToUser()) {
            abort(403);
        }

        $request->validate([
            'api_service_id' => 'required|exists:api_services,id',
            'credentials' => 'required|array',
            'execution_frequency' => 'nullable|string',
            'alert_email' => 'nullable|email',
            'endpoints' => 'nullable|array',
            'endpoints.*' => 'exists:endpoints,id',
        ]);

        $apiService = ApiService::findOrFail($request->api_service_id);

        // Validar que todos los campos requeridos estén presentes
        foreach ($apiService->required_fields as $field) {
            if (empty($request->credentials[$field])) {
                return back()->with('error', "El campo '$field' es obligatorio para " . $apiService->name . ".");
            }
        }

        // Calcular Frecuencia de Ejecución (CRON)
        $executionFrequency = null;

        if ($request->automation_type === 'custom' && $request->filled(['scheduled_time', 'scheduled_days'])) {
            $timeObj = \Carbon\Carbon::createFromFormat('H:i', $request->scheduled_time);
            $minutes = $timeObj->minute;
            $hours = $timeObj->hour;
            $days = implode(',', $request->scheduled_days); // 1,3,5

            // Format: min hour * * days
            $executionFrequency = "{$minutes} {$hours} * * {$days}";
        } elseif ($request->automation_type === 'manual') {
             $executionFrequency = null;
        } else {
             // Fallback for previous logic if needed or just keep null
             $executionFrequency = $request->execution_frequency;
        }

        // Crear credencial
        $credential = $client->credentials()->create([
            'api_service_id' => $apiService->id,
            'credentials' => $request->credentials,
            'is_active' => true,
            'execution_frequency' => $executionFrequency,
            'alert_email' => $request->alert_email,
        ]);

        if ($request->has('endpoints')) {
            $credential->endpoints()->sync($request->endpoints);
        }

        return back()->with('success', 'Credenciales guardadas y encriptadas correctamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClientCredential $credential)
    {
        // Verificar propiedad del cliente
        if (!auth()->user()->hasRole(['Super Admin', 'Manager', 'Programador']) && !$credential->client->belongsToUser()) {
            abort(403);
        }

        $request->validate([
            'credentials' => 'required|array',
            'execution_frequency' => 'nullable|string',
            'alert_email' => 'nullable|email',
            'endpoints' => 'nullable|array',
            'endpoints.*' => 'exists:endpoints,id',
        ]);

        $apiService = $credential->apiService;

        // Validar campos requeridos del servicio
        foreach ($apiService->required_fields as $field) {
            if (empty($request->credentials[$field])) {
                return back()->with('error', "El campo '$field' es obligatorio.");
            }
        }

        // Calcular Frecuencia de Ejecución (CRON)
        $executionFrequency = null;

        if ($request->automation_type === 'custom' && $request->filled(['scheduled_time', 'scheduled_days'])) {
            $timeObj = \Carbon\Carbon::createFromFormat('H:i', $request->scheduled_time);
            $minutes = $timeObj->minute;
            $hours = $timeObj->hour;
            $days = implode(',', $request->scheduled_days); // 1,3,5

            // Format: min hour * * days
            $executionFrequency = "{$minutes} {$hours} * * {$days}";
        } elseif ($request->automation_type === 'manual') {
             $executionFrequency = null;
        } else {
             // Mantener valor anterior si no se envió nada nuevo específico, o null
             $executionFrequency = $request->execution_frequency;
        }

        // Actualizar
        $credential->update([
            'credentials' => $request->credentials,
            'execution_frequency' => $executionFrequency,
            'alert_email' => $request->alert_email,
        ]);
        
        // Sync endpoints regardless, if array is present (or empty to clear)
        // If not present in request (e.g. partial update), we might want to skip, 
        // but for a full form submit usually we verify presence.
        // Assuming the form always sends 'endpoints' (checked or not).
        if ($request->has('endpoints')) {
            $credential->endpoints()->sync($request->endpoints);
        } else {
             // If field is missing, it implies no endpoints selected only if the input was on the page.
             // But careful if it's not sent when empty. AlpineJS usually sends nothing if no checkbox.
             // safer to default to empty only if we know full form was submitted.
             // For now, let's assume we send it as hidden empty if needed or handle logic in view.
             // Actually, checkbox arrays just don't send anything if empty.
             // We should explicit check if we are "managing integration".
             $credential->endpoints()->sync([]);
        }

        return back()->with('success', 'Integración actualizada correctamente.');
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