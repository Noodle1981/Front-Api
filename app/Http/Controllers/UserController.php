<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Display the authenticated user.
     */
    public function showCurrentUser()
    {
        $user = auth()->user();
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->is_admin = $request->has('is_admin');
        $user->settings = ['commission_rate' => $request->commission_rate ?? 0];
        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.','. $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->is_admin = $request->has('is_admin');
        $user->settings = ['commission_rate' => $request->commission_rate ?? 0];

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')->with('error', 'No puedes eliminar a tu propio usuario.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado con éxito.');
    }
}