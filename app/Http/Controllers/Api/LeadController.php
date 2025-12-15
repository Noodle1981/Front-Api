<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\Request;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\DealResource;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = $request->user()->leads()
            ->where('status', '!=', 'convertido') // Solo mostramos leads activos
            ->latest()
            ->paginate(15);
        return LeadResource::collection($leads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:leads,email',
            'phone' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        $lead = $request->user()->leads()->create($validated + ['status' => 'nuevo']);

        return new LeadResource($lead);
    }

    public function show(Request $request, Lead $lead)
    {
        if ($request->user()->id !== $lead->user_id) {
            return response()->json(['error' => 'Not Found'], 404);
        }
        return new LeadResource($lead);
    }

    public function update(Request $request, Lead $lead)
    {
        if ($request->user()->id !== $lead->user_id) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'company' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|max:255|unique:leads,email,' . $lead->id,
            'phone' => 'sometimes|nullable|string|max:255',
            'source' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|string|in:nuevo,contactado,calificado,perdido',
        ]);

        $lead->update($validated);
        return new LeadResource($lead);
    }

    public function destroy(Request $request, Lead $lead)
    {
        if ($request->user()->id !== $lead->user_id) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        $lead->delete();
        return response()->noContent();
    }

    public function convert(Request $request, Lead $lead)
    {
        // 1. Seguridad: Solo el dueño del lead puede convertirlo.
        if ($request->user()->id !== $lead->user_id) {
            return response()->json(['error' => 'Not Found'], 404);
        }

        // 2. Lógica de negocio: No se puede convertir un lead ya convertido o perdido.
        if ($lead->status === 'convertido' || $lead->status === 'perdido') {
            return response()->json(['message' => 'This lead cannot be converted.'], 422);
        }

        // 3. ¡La Magia! Usamos una transacción para asegurar que todo o nada se ejecute.
        $result = DB::transaction(function () use ($lead, $request) {
            // Crear el Cliente
            $client = Client::create([
                'name' => $lead->company ?? $lead->name, // Usa la compañía o el nombre del lead
                'company' => $lead->company,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'user_id' => $request->user()->id,
            ]);

            // Crear el Contacto
            $contact = $client->contacts()->create([
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
            ]);

            // Crear el Deal en la primera etapa del pipeline
            $deal = $client->deals()->create([
                'name' => 'Oportunidad inicial para ' . $client->name,
                'user_id' => $request->user()->id,
                'deal_stage_id' => 1, // Asumimos que la primera etapa siempre tiene el ID 1
            ]);

            // Actualizar el estado del Lead
            $lead->update(['status' => 'convertido']);

            // Devolver los nuevos recursos creados
            return [
                'client' => new ClientResource($client),
                'contact' => new ContactResource($contact),
                'deal' => new DealResource($deal),
            ];
        });

        return response()->json([
            'message' => 'Lead converted successfully!',
            'data' => $result,
        ], 201);
    }
        

}