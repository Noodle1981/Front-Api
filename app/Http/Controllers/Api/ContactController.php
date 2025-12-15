<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource; // <-- ¡Ya lo tienes!
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ContactController extends Controller
{
    public function create(Client $client)
    {
        if (Auth::user()->id !== $client->user_id) abort(403);
        return view('contacts.create', compact('client'));
    }

    public function store(Request $request, Client $client)
    {
        if (Auth::user()->id !== $client->user_id) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:contacts,email',
            'phone' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $client->contacts()->create($validated);
        return redirect()->route('clients.show', $client)->with('success', '¡Contacto añadido con éxito!');
    }

    // --- ¡NUEVOS MÉTODOS! ---

    public function edit(Client $client, Contact $contact)
    {
        // La regla ->scoped() en la ruta ya nos da una capa de seguridad
        return view('contacts.edit', compact('client', 'contact'));
    }

    public function update(Request $request, Client $client, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('contacts')->ignore($contact->id)],
            'phone' => 'nullable|string',
            'position' => 'nullable|string',
        ]);

        $contact->update($validated);
        return redirect()->route('clients.show', $client)->with('success', '¡Contacto actualizado con éxito!');
    }

    public function destroy(Client $client, Contact $contact)
    {
        $contact->delete();
        return redirect()->route('clients.show', $client)->with('success', '¡Contacto eliminado con éxito!');
    }
}