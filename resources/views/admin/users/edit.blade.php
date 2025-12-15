@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Editar Usuario: {{ $user->name }}</h2>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nombre -->
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $user->name) }}"
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
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña (Opcional) -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nueva Contraseña <span class="text-gray-400">(opcional)</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Dejar en blanco para mantener la contraseña actual. Mínimo 8 caracteres si se modifica.</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Es Administrador -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_admin" 
                               id="is_admin" 
                               value="1"
                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-shield-alt text-blue-600 mr-1"></i>
                            Usuario Administrador
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Los administradores tienen acceso completo al sistema</p>
                </div>

                <!-- Usuario Activo -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            <i class="fas fa-user-check text-green-600 mr-1"></i>
                            Usuario Activo
                        </span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Los usuarios inactivos no pueden iniciar sesión</p>
                </div>

                <!-- Información adicional -->
                <div class="mb-6 p-4 bg-gray-50 rounded-md">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Información del Usuario</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Fecha de Registro:</span>
                            <span class="font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Última Actualización:</span>
                            <span class="font-medium">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.users.index') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Zona de Peligro -->
    <div class="bg-red-50 border border-red-200 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-800 mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i> Zona de Peligro
            </h3>
            <p class="text-sm text-red-600 mb-4">
                La eliminación de un usuario es permanente y no se puede deshacer.
            </p>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition">
                    <i class="fas fa-trash-alt mr-2"></i> Eliminar Usuario
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
