@extends('layouts.admin')

@section('content')
    <div class="space-y-6">

        <!-- 1. Tarjeta de Bienvenida / Resumen General (Arriba) -->
        <div class="bg-gradient-to-r from-brand-dark to-[#0C263B] rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2">Panel de Control General</h2>
                    <p class="text-blue-200">Visión global de la plataforma, usuarios y servicios conectados.</p>
                </div>
                <div class="hidden sm:block">
                    <i class="fas fa-chart-pie text-6xl text-white/10"></i>
                </div>
            </div>
        </div>

        <!-- 2. Grid de Métricas (Usuarios, Clientes, APIs, Scripts) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Usuarios -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Usuarios Activos</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['activeUsers'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Clientes -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Clientes Totales</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['totalClients'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- APIs Configuradas -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-plug text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">APIs Disponibles</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['totalApis'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Scripts Activos -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-pink-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-pink-100 text-pink-600 mr-4">
                        <i class="fas fa-robot text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Scripts Activos</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['activeScripts'] ?? 0 }}</div>
                        <div class="text-xs text-gray-400 mt-1">Automatizaciones programadas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Distribución de Clientes por Usuario -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700">Carga de Clientes por Usuario</h3>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-medium border-b">
                            <tr>
                                <th class="px-6 py-3">Usuario</th>
                                <th class="px-6 py-3 text-right">Cant. Clientes</th>
                                <th class="px-6 py-3">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($usersWithClients as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-3 text-xs">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span
                                            class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $user->clients_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->is_active)
                                            <span class="text-green-500 text-xs"><i class="fas fa-circle"></i> Activo</span>
                                        @else
                                            <span class="text-red-500 text-xs"><i class="fas fa-circle"></i> Inactivo</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                        No hay usuarios con clientes asignados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Espacio para futuras gráficas (Placeholder) -->
            <div
                class="bg-white shadow-sm rounded-lg p-6 flex items-center justify-center border-2 border-dashed border-gray-200">
                <div class="text-center text-gray-400">
                    <i class="fas fa-chart-bar text-4xl mb-3"></i>
                    <h3 class="text-lg font-medium">Estadísticas de Ejecución</h3>
                    <p class="text-sm">Próximamente: Gráfica de scripts fallidos vs exitosos.</p>
                </div>
            </div>
        </div>

    </div>
@endsection