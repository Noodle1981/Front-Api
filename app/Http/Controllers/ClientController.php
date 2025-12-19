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
        $user = auth()->user();
        $isGlobal = $user->hasRole(['Super Admin', 'Manager', 'Programador']);
        
        // User Context Filter
        $userFilter = $request->input('user_filter');
        $selectedUser = null;
        if ($userFilter && $isGlobal) {
            $selectedUser = \App\Models\User::find($userFilter);
        }
        
        // Base query with context
        if ($selectedUser) {
            $query = Client::where('user_id', $selectedUser->id);
        } else {
            $query = Client::forCurrentUser();
        }
        
        $filter = $request->get('filter', 'activos');

        if ($filter === 'inactivos') {
            $query = $query->where('active', false);
        } else {
            $query = $query->where('active', true);
        }

        // Estadísticas completas
        $baseStatsQuery = $selectedUser ? Client::where('user_id', $selectedUser->id) : Client::forCurrentUser();
        
        $stats = [
            'total_clients' => $baseStatsQuery->count(),
            'active_clients' => $baseStatsQuery->where('active', true)->count(),
            'inactive_clients' => $baseStatsQuery->where('active', false)->count(),
            'headquarters' => $baseStatsQuery->whereNull('parent_id')->count(),
            'branches' => $baseStatsQuery->whereNotNull('parent_id')->count(),
            'with_apis' => $baseStatsQuery->whereHas('credentials', function($q) {
                $q->where('is_active', true);
            })->count(),
        ];

        $clients = $query->latest()->paginate(10)->appends($request->except('page'));
        
        
        // User context selector data
        $contextUsers = $isGlobal ? \App\Models\User::role('Operador')->orderBy('name')->get(['id', 'name']) : collect();
        
        return view('clients.index', compact('clients', 'stats', 'filter', 'contextUsers', 'selectedUser'));
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


        $client->load(['credentials.apiService', 'credentials.endpoints', 'parent', 'children']);
        $apiServices = \App\Models\ApiService::with('endpoints')->get();

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


        // Verificar permiso de borrado (Spatie)
        if (!Auth::user()->can('delete clients')) {
            abort(403, 'No tienes permiso para eliminar clientes.');
        }

        $client->delete();
        return redirect()->route('clients.index')->with('success', '¡Cliente eliminado con éxito!');
    }

    public function data(Client $client)
    {


        return response()->json([
            'message' => 'Endpoint deprecated',
        ]);
    }

    public function deactivate(Request $request, Client $client)
    {
        $data = [
            'active' => false,
            'deactivation_reason' => $request->has('reason') ? $request->input('reason') : 'Otros motivos',
        ];
        
        if ($request->has('reason')) {
            $note = "\n[" . now()->format('Y-m-d') . "] Suspendido: " . $request->input('reason');
            $client->internal_notes .= $note;
        }

        $client->update($data);
        return redirect()->route('clients.index')->with('success', 'Cliente desactivado correctamente.');
    }

    public function activate(Client $client)
    {
        $client->update([
            'active' => true,
            'deactivation_reason' => null
        ]);
        return redirect()->route('clients.index')->with('success', 'Cliente reactivado correctamente.');
    }
}