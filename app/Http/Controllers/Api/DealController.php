<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DealResource;
use App\Models\Client;
use App\Models\Deal;
use Illuminate\Http\Request;
use App\Models\DealStage;

class DealController extends Controller
{
    // Dejamos los otros métodos vacíos por ahora
     public function index(Request $request)
    {
        // 1. Obtenemos todas las etapas del pipeline, ordenadas.
        $dealStages = DealStage::orderBy('order')->get();

        // 2. Usamos "map" para transformar cada etapa.
        $pipelineData = $dealStages->map(function ($stage) use ($request) {
            
            // 3. Para cada etapa, buscamos los deals que le pertenecen
            //    y que son del usuario autenticado.
            $deals = $stage->deals()
               ->where('user_id', $request->user()->id)
               ->where('status', 'open') // <-- ¡LA LÍNEA MÁGICA!
               ->with('client')
               ->get();

            // 4. Devolvemos una nueva estructura para cada etapa
            return [
                'id' => $stage->id,
                'name' => $stage->name,
                'deals' => DealResource::collection($deals) // Usamos el Resource para formatear los deals
            ];
        });

        // 5. Devolvemos el array completo
        return response()->json($pipelineData);
    }
    public function show(Deal $deal) { /* TODO */ }
    public function update(Request $request, Deal $deal)
{
    // Seguridad: Asegurarse de que el usuario solo puede modificar sus propios deals
    if ($request->user()->id !== $deal->user_id) {
        return response()->json(['error' => 'Not Found'], 404);
    }

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'value' => 'sometimes|numeric|min:0',
        'expected_close_date' => 'sometimes|nullable|date',
        // ¡La nueva validación clave!
        'deal_stage_id' => 'sometimes|integer|exists:deal_stages,id' 
    ]);

    $deal->update($validated);

    // Devolvemos el deal actualizado, con su cliente cargado para tener el contexto
    return new DealResource($deal->load('client'));

    $validated = $request->validate([
    // ... los otros campos 'sometimes'
    'deal_stage_id' => 'sometimes|integer|exists:deal_stages,id',
    // ¡LA NUEVA REGLA!
    'status' => 'sometimes|string|in:open,won,lost' 

    
]);
}
    public function destroy(Deal $deal) { /* TODO */ }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'nullable|numeric|min:0',
            'expected_close_date' => 'nullable|date',
            'client_id' => 'required|integer|exists:clients,id',
            'deal_stage_id' => 'required|integer|exists:deal_stages,id' // La etapa del pipeline
        ]);

        // --- Comprobación de Seguridad ---
        $client = Client::findOrFail($validated['client_id']);
        if ($request->user()->id !== $client->user_id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Creamos el Deal asociado a ese cliente y al usuario actual
        $deal = $client->deals()->create($validated + ['user_id' => $request->user()->id]);

        return new DealResource($deal);
    }

    public function getWonDeals(Request $request)
    {
        $deals = $request->user()->deals()
                         ->where('status', 'won')
                         ->with('client') // Carga el cliente para tener contexto
                         ->latest('updated_at') // Muestra los ganados más recientemente primero
                         ->paginate(15);

        return DealResource::collection($deals);
    }

    /**
     * Display a listing of lost deals.
     */
    public function getLostDeals(Request $request)
    {
        $deals = $request->user()->deals()
                         ->where('status', 'lost')
                         ->with('client')
                         ->latest('updated_at')
                         ->paginate(15);

        return DealResource::collection($deals);
    }
}
    
