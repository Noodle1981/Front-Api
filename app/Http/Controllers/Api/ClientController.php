<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = $request->user()->clients()->latest()->paginate(10);
        return ClientResource::collection($clients);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client = $request->user()->clients()->create($validated);

        return new ClientResource($client);
    }

   public function show(Request $request, Client $client)
{
    // 1. Seguridad (esto ya lo tienes)
    if ($request->user()->id !== $client->user_id) {
        return response()->json(['error' => 'Not Found'], 404);
    }

    // 2. Carga eficiente de TODAS las relaciones necesarias, sin espacios extra
    $client->load([
        'contacts', 
        'deals.activities.user', // Carga los deals, y por cada deal, sus actividades y el usuario de la actividad
        'activities.user'        // Carga las actividades directas del cliente y su usuario
    ]);

    // 3. Obtenemos las actividades directas del cliente
    $clientActivities = $client->activities;

    // 4. Obtenemos las actividades de TODOS los deals de este cliente
    $dealActivities = $client->deals->pluck('activities')->flatten();
    
    // 5. Unimos las dos colecciones en una sola
    $fullActivityLog = $clientActivities->merge($dealActivities);
    
    // 6. Ordenamos el historial completo por fecha de creación, del más reciente al más antiguo
    $sortedActivityLog = $fullActivityLog->sortByDesc('created_at');

    // 7. Reemplazamos la relación 'activities' del cliente con nuestro nuevo historial consolidado
    //    El ->values() es para re-indexar el array y que se muestre como un JSON array [ ... ]
    $client->setRelation('activities', $sortedActivityLog->values());

    // 8. Devolvemos el resource, que ahora usará nuestro historial consolidado
    return new ClientResource($client);
}

    public function update(Request $request, Client $client)
    {
        // Política de seguridad
        if ($request->user()->id !== $client->user_id) {
            return response()->json(['error' => 'Not Found'], 404);
        }
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return new ClientResource($client);
    }

    public function destroy(Request $request, Client $client)
    {
        // Política de seguridad
        if ($request->user()->id !== $client->user_id) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $client->delete();

        return response()->noContent();
    }
}