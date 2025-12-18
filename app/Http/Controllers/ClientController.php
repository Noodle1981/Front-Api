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
            $query = $query->where('active', false);
        } else {
            $query = $query->where('active', true);
        }

        // Para estadísticas
        $stats = [
            'total_clients' => Client::forCurrentUser()->count(),
            'active_clients' => Client::forCurrentUser()->where('active', true)->count(),
        ];

        $clients = $query->latest()->paginate(10);
        return view('clients.index', compact('clients', 'stats', 'filter'));
    }

    public function create()
    {
        // Traer posibles sedes (clientes que NO son sucursal)
        $parents = Client::forCurrentUser()->whereNull('parent_id')->orderBy('company', 'asc')->get();
        return view('clients.create', compact('parents'));
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

        $client->load(['credentials.apiService', 'parent', 'children']);
        $apiServices = \App\Models\ApiService::all();

        return view('clients.show', compact('client', 'apiServices'));
    }

    public function edit(Client $client)
    {
        if (Auth::user()->id !== $client->user_id)
            abort(404);

        // Traer posibles sedes (clientes que NO son sucursal y NO son el mismo)
        $parents = Client::forCurrentUser()
            ->whereNull('parent_id')
            ->where('id', '!=', $client->id)
            ->orderBy('company', 'asc')
            ->get();

        return view('clients.edit', compact('client', 'parents'));
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
        if (Auth::user()->id !== $client->user_id) {
            abort(404);
        }

        // Verificar permiso de borrado (Spatie)
        if (!Auth::user()->can('delete clients')) {
            abort(403, 'No tienes permiso para eliminar clientes.');
        }

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
        $client->update(['active' => false]);
        return redirect()->route('clients.index')->with('success', 'Cliente desactivado correctamente.');
    }

    public function activate(Client $client)
    {
        $client->update(['active' => true]);
        return redirect()->route('clients.index')->with('success', 'Cliente reactivado correctamente.');
    }
}