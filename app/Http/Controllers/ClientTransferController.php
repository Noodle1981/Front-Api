<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ClientTransferController extends Controller
{
    public function edit(Client $client)
    {
        // Solo Analistas o Admins
        if (!Auth::user()->can('reassign clients') && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $users = User::role('User')->where('id', '!=', $client->user_id)->get();

        return view('clients.transfer', compact('client', 'users'));
    }

    public function update(Request $request, Client $client)
    {
        if (!Auth::user()->can('reassign clients') && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:255',
        ]);

        $oldUser = $client->user ? $client->user->name : 'Nadie';
        $newUser = User::find($validated['user_id']);

        $client->update(['user_id' => $validated['user_id']]);

        // Opcional: Loguear esto en una auditoría si existiera tabla
        // Log::info("Client {$client->id} transferred from $oldUser to {$newUser->name} by " . Auth::user()->name);

        return redirect()->route('analyst.clients.index')
            ->with('success', "Cliente transferido correctamente de $oldUser a {$newUser->name}.");
    }
}
