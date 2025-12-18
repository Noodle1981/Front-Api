<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle incoming webhooks.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $service  Slug of the service (e.g. 'mercado-pago')
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, string $service)
    {
        // 1. Verificar si el servicio existe
        $apiService = ApiService::where('slug', $service)->first();

        if (!$apiService) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // 2. Loggear el payload (para debugging inicial)
        Log::info("Webhook recibido de [{$service}]:", $request->all());

        // 3. Procesar según el servicio (Placeholder para lógica futura)
        // Aquí podríamos disparar eventos o jobs específicos
        // Ej: WebhookReceived::dispatch($apiService, $request->all());

        return response()->json(['message' => 'Webhook received successfully'], 200);
    }
}
