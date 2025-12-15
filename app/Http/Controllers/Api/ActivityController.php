<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityResource;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validated = $request->validate([
        'type' => ['required', Rule::in(['call', 'meeting', 'email', 'note'])],
        'description' => 'required|string',
        'loggable_id' => 'required|integer',
        'loggable_type' => ['required', 'string', Rule::in(['App\Models\Client', 'App\Models\Deal', 'App\Models\Contact'])]
    ]);

    $loggable = $validated['loggable_type']::find($validated['loggable_id']);

    if (!$loggable) {
        return response()->json(['message' => 'The related resource was not found.'], 404);
    }
    
    // Aquí podrías añadir una política de seguridad

    $activity = $loggable->activities()->create([
        'user_id' => $request->user()->id,
        'type' => $validated['type'],
        'description' => $validated['description'],
    ]);

    // Devolvemos el resource con el usuario ya cargado
    return new ActivityResource($activity->load('user'));
}
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // app/Http/Resources/ActivityResource.php
public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'type' => $this->type, // 'call', 'meeting', 'email', 'note'
        'description' => $this->description,
        'createdAt' => $this->created_at->diffForHumans(), // "hace 5 minutos"
        'user' => $this->whenLoaded('user', fn() => $this->user->name),
        'relatedTo' => [
            'type' => class_basename($this->loggable_type),
            'id' => $this->loggable_id,
        ],
    ];
}
}
