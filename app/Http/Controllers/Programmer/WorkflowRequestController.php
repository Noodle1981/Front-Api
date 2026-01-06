<?php

namespace App\Http\Controllers\Programmer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkflowRequest;

class WorkflowRequestController extends Controller
{
    public function index()
    {
        $requests = WorkflowRequest::with(['user', 'client'])
            ->latest()
            ->get();

        return view('programmer.workflows.requests', compact('requests'));
    }

    public function accept(WorkflowRequest $request)
    {
        $request->update(['status' => 'in_progress']);
        return redirect()->route('programmer.workflows.requests.execute', $request)
            ->with('success', 'Pedido aceptado. Ahora puedes ejecutar el workflow.');
    }

    public function execute(WorkflowRequest $request)
    {
        // Verificar que el pedido esté en progreso
        if ($request->status !== 'in_progress') {
            return redirect()->route('programmer.workflows.requests')
                ->with('error', 'Este pedido no está disponible para ejecución.');
        }

        return view('programmer.workflows.execute-request', compact('request'));
    }

    public function reject(WorkflowRequest $request)
    {
        $request->update(['status' => 'rejected']);
        return back()->with('warning', 'Pedido rechazado.');
    }
}
