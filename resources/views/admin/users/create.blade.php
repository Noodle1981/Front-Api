@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Crear Nuevo Usuario</h2>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <!-- Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Mínimo 8 caracteres</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Contraseña <span class="text-red-500">*</span>
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                <!-- Es Administrador -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_admin" 
                               id="is_admin" 
                               value="1"
                               {{ old('is_admin') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-shield-alt text-blue-600 mr-1"></i>
                            Usuario Administrador
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Los administradores tienen acceso completo al sistema</p>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i> Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
