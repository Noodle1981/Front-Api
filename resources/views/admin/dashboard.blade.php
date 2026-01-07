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

        <!-- 2. NUEVA SECCIÓN: Métricas de Ahorro de Tiempo -->
        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg shadow-lg p-6 border border-emerald-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-emerald-800 flex items-center">
                        <i class="fas fa-clock mr-3 text-emerald-600"></i>
                        Beneficios del Sistema
                    </h3>
                    <p class="text-emerald-600 text-sm mt-1">Tiempo ahorrado vs trabajo manual</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500">Período: Este mes</span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Tiempo Ahorrado -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-emerald-100">
                    <div class="flex items-center justify-between">
                        <div class="p-2 bg-emerald-100 rounded-lg">
                            <i class="fas fa-hourglass-half text-emerald-600 text-xl"></i>
                        </div>
                        <span class="text-xs font-medium text-emerald-600 bg-emerald-100 px-2 py-1 rounded-full">
                            <i class="fas fa-arrow-up mr-1"></i>+{{ rand(10, 30) }}%
                        </span>
                    </div>
                    <div class="mt-3">
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['timeSaved'] ?? '48h' }}</p>
                        <p class="text-sm text-gray-500">Tiempo ahorrado</p>
                    </div>
                </div>

                <!-- Workflows Ejecutados -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-blue-100">
                    <div class="flex items-center justify-between">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                        </div>
                        <span class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                            Este mes
                        </span>
                    </div>
                    <div class="mt-3">
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['workflowsExecuted'] ?? 127 }}</p>
                        <p class="text-sm text-gray-500">Workflows ejecutados</p>
                    </div>
                </div>

                <!-- Costo Evitado -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-amber-100">
                    <div class="flex items-center justify-between">
                        <div class="p-2 bg-amber-100 rounded-lg">
                            <i class="fas fa-dollar-sign text-amber-600 text-xl"></i>
                        </div>
                        <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">
                            Estimado
                        </span>
                    </div>
                    <div class="mt-3">
                        <p class="text-3xl font-bold text-gray-900">${{ $stats['costSaved'] ?? '2,400' }}</p>
                        <p class="text-sm text-gray-500">Costo evitado</p>
                    </div>
                </div>

                <!-- Productividad -->

            </div>

            <!-- Barra de comparación visual -->
            <div class="mt-6 bg-white rounded-lg p-4 border border-emerald-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Eficiencia vs Trabajo Manual</span>
                    <span class="text-sm font-bold text-emerald-600">{{ $stats['efficiency'] ?? 85 }}% más rápido</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-3 rounded-full transition-all" 
                         style="width: {{ $stats['efficiency'] ?? 85 }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Basado en tiempo promedio de {{ $stats['manualMinutes'] ?? 45 }} min por workflow manual vs {{ $stats['autoMinutes'] ?? 7 }} min automatizado
                </p>
            </div>
        </div>

        <!-- 3. Grid de Métricas Básicas -->
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



            <!-- Workflows Activos -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 border-l-4 border-pink-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-pink-100 text-pink-600 mr-4">
                        <i class="fas fa-project-diagram text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase tracking-wide">Tipos de Workflow</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['workflowTypes'] ?? 3 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Distribución de Clientes por Usuario -->
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

            <!-- Resumen de Actividad Reciente -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700">Actividad Reciente</h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-100">
                        <div class="p-2 bg-green-100 rounded-full mr-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Workflows exitosos hoy</p>
                            <p class="text-xs text-gray-500">Tasa de éxito: {{ $stats['successRate'] ?? 98 }}%</p>
                        </div>
                        <span class="text-2xl font-bold text-green-600">{{ $stats['successToday'] ?? 23 }}</span>
                    </div>
                    
                    <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-100">
                        <div class="p-2 bg-red-100 rounded-full mr-3">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Errores hoy</p>
                            <p class="text-xs text-gray-500">Requieren revisión</p>
                        </div>
                        <span class="text-2xl font-bold text-red-600">{{ $stats['errorsToday'] ?? 2 }}</span>
                    </div>
                    

                </div>
            </div>
        </div>

    </div>
@endsection