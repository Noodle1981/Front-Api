<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkflowRequestController extends Controller
{
    /**
     * Store a new workflow request.
     * 
     * TODO: Implementar lógica de guardado cuando se cree la tabla workflow_requests
     * Por ahora, simula el guardado y redirige con mensaje de éxito.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'workflow_type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'expected_date' => 'nullable|date|after:today',
        ]);

        // TODO: Crear tabla workflow_requests y model WorkflowRequest
        // WorkflowRequest::create([
        //     'user_id' => auth()->id(),
        //     'client_id' => $validated['client_id'],
        //     'workflow_type' => $validated['workflow_type'],
        //     'title' => $validated['title'],
        //     'description' => $validated['description'],
        //     'priority' => $validated['priority'],
        //     'expected_date' => $validated['expected_date'],
        //     'status' => 'pending',
        // ]);

        // Por ahora solo mostramos mensaje de éxito
        return redirect()
            ->route('dashboard')
            ->with('success', '¡Solicitud enviada! El equipo de programación la revisará pronto.');
    }
}
