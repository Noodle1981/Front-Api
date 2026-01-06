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
        $request->update(['status' => 'accepted']);
        return back()->with('success', 'Pedido aceptado correctamente.');
    }

    public function reject(WorkflowRequest $request)
    {
        $request->update(['status' => 'rejected']);
        return back()->with('warning', 'Pedido rechazado.');
    }
}
