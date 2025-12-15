@extends('admin.layout')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Panel de Administración - Portal de Clientes</h2>
        </div>

        <!-- Estadísticas generales (desde el controlador admin) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 text-xl mb-2">Usuarios Activos</div>
                <div class="text-3xl font-bold">{{ $stats['activeUsers'] ?? 0 }}</div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-gray-900 text-xl mb-2">Clientes Registrados</div>
                <div class="text-3xl font-bold">{{ $stats['totalClients'] ?? 0 }}</div>
            </div>
        </div>

        {{-- Eliminated Charts and CRM specifics --}}

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Bienvenido al Panel de Administración</h3>
                <p class="text-gray-600">Desde aquí puede gestionar los usuarios y la información de los clientes del
                    portal.</p>
            </div>
        </div>

    </div>
@endsection