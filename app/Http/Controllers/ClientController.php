<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Rules\ValidPhoneNumber;
use App\Http\Requests\ClientRequest;


class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::forCurrentUser();
        $filter = $request->get('filter', 'activos');
        if ($filter === 'inactivos') {
            $query = $query->where('client_status', 'inactivo');
        } else {
            $query = $query->where('client_status', '!=', 'inactivo');
        }
        // Para estadísticas, usar queries independientes para evitar duplicación y errores de brackets
        $stats = [
            'total_clients' => Client::forCurrentUser()->count(),
            'active_clients' => Client::forCurrentUser()->where('client_status', '!=', 'inactivo')->count(),
        ];
        $clients = $query->latest()->paginate(10);
        return view('clients.index', compact('clients', 'stats', 'filter'));
    }

    public function create()
    {
        return view('clients.create');
    }


    public function store(ClientRequest $request)
    {
        $validated = $request->validated();
        Auth::user()->clients()->create($validated);
        return redirect()->route('clients.index')->with('success', '¡Cliente creado con éxito!');
    }


    public function show(Client $client)
    {
        // 1. Seguridad: Solo el dueño puede ver los detalles.
        if (Auth::user()->id !== $client->user_id) {
            abort(404);
        }

        // 2. Carga eficiente de TODAS las relaciones en una sola llamada.
        // deal, activities removed

        // 3. Pasamos el cliente
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        if (Auth::user()->id !== $client->user_id)
            abort(404);
        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        if (Auth::user()->id !== $client->user_id)
            abort(404);
        $validated = $request->validated();
        $client->update($validated);
        return redirect()->route('clients.index')->with('success', '¡Cliente actualizado con éxito!');
    }

    public function destroy(Client $client)
    {
        if (Auth::user()->id !== $client->user_id)
            abort(404);
        $client->delete();
        return redirect()->route('clients.index')->with('success', '¡Cliente eliminado con éxito!');
    }

    public function data(Client $client)
    {
        if (Auth::user()->id !== $client->user_id) {
            abort(404);
        }

        return response()->json([
            'message' => 'Endpoint deprecated',
        ]);
    }

    public function deactivate(Client $client)
    {
        $client->update(['client_status' => 'inactivo']);
        return redirect()->route('clients.index')->with('success', 'Cliente desactivado correctamente.');
    }

    public function activate(Client $client)
    {
        $client->update(['client_status' => 'activo']);
        return redirect()->route('clients.index')->with('success', 'Cliente reactivado correctamente.');
    }
}