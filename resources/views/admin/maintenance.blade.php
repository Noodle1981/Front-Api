@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Mantenimiento del Sistema</h2>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Optimización del Sistema -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-rocket text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Optimización</h3>
                        <p class="text-sm text-gray-600">Mejora el rendimiento del sistema</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <form action="{{ route('admin.maintenance.optimize') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                            <i class="fas fa-bolt mr-2"></i> Optimizar Aplicación
                        </button>
                    </form>

                    <form action="{{ route('admin.maintenance.clear-cache') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition text-sm">
                            <i class="fas fa-broom mr-2"></i> Limpiar Caché
                        </button>
                    </form>

                    <form action="{{ route('admin.maintenance.clear-views') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition text-sm">
                            <i class="fas fa-file-code mr-2"></i> Limpiar Vistas Compiladas
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Respaldos -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 p-3 bg-green-100 rounded-full">
                        <i class="fas fa-database text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Respaldos</h3>
                        <p class="text-sm text-gray-600">Gestiona copias de seguridad</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <form action="{{ route('admin.maintenance.backup') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition text-sm">
                            <i class="fas fa-save mr-2"></i> Crear Respaldo Ahora
                        </button>
                    </form>

                    @if($latestBackup)
                        <div class="p-3 bg-gray-50 rounded-md text-sm">
                            <p class="text-gray-600 mb-1">Último respaldo:</p>
                            <p class="font-medium text-gray-800">{{ basename($latestBackup) }}</p>
                        </div>
                    @else
                        <div class="p-3 bg-yellow-50 rounded-md text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            No hay respaldos disponibles
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-info-circle text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Información del Sistema</h3>
                        <p class="text-sm text-gray-600">Detalles de la aplicación</p>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Versión de Laravel:</span>
                        <span class="font-medium">{{ app()->version() }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Versión de PHP:</span>
                        <span class="font-medium">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Entorno:</span>
                        <span class="font-medium">{{ config('app.env') }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Base de Datos:</span>
                        <span class="font-medium">{{ config('database.default') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Limpieza de Datos -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 p-3 bg-red-100 rounded-full">
                        <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Limpieza de Datos</h3>
                        <p class="text-sm text-gray-600">Elimina datos antiguos</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <form action="{{ route('admin.maintenance.clean-logs') }}" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de que deseas limpiar los logs antiguos?');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition text-sm">
                            <i class="fas fa-file-alt mr-2"></i> Limpiar Logs Antiguos
                        </button>
                    </form>

                    <form action="{{ route('admin.maintenance.clean-sessions') }}" method="POST"
                          onsubmit="return confirm('¿Estás seguro? Esto cerrará todas las sesiones activas.');">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm">
                            <i class="fas fa-users-slash mr-2"></i> Limpiar Sesiones Expiradas
                        </button>
                    </form>
                </div>

                <div class="mt-4 p-3 bg-red-50 rounded-md">
                    <p class="text-xs text-red-800">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Advertencia:</strong> Estas acciones son irreversibles
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
