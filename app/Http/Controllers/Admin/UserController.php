<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Lista de usuarios con métricas
     */
    public function index()
    {
        $users = User::withCount(['clients'])
            ->orderBy('name')
            ->paginate(15);

        $adminCount = User::where('is_admin', true)->count();

        return view('admin.users.index', compact('users', 'adminCount'));
    }

    /**
     * Formulario para crear usuario
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Almacena un nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['is_admin'] ?? false,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Muestra el formulario de edición
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Actualiza un usuario
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($validated['password'] ?? false) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario
     */
    public function destroy(User $user)
    {
        // Verificar que no sea el último admin
        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'No se puede eliminar el último administrador.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}