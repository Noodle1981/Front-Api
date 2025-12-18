<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings text-3xl font-extrabold text-primary-light tracking-tight drop-shadow mb-2">
            Dashboard
        </h2>
    </x-slot>

    <div class="bg-background min-h-screen w-full p-4 md:p-6 space-y-8">

        {{-- 1. SECCIÓN: ATENCIÓN REQUERIDA --}}
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bell text-yellow-500 mr-2"></i> Atención Requerida
            </h3>
            
            @if($attentionRequired->isEmpty())
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-center">
                    <div class="flex-shrink-0 text-green-500">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">Todo en orden. No hay alertas críticas en tus clientes hoy.</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($attentionRequired as $client)
                        <a href="{{ route('clients.show', $client) }}" class="block bg-white border-l-4 border-red-500 rounded-r shadow hover:shadow-md transition p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $client->fantasy_name ?? $client->company }}</h4>
                                    <p class="text-sm text-gray-500">{{ $client->cuit }}</p>
                                </div>
                                <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded-full">
                                    {{ $client->api_logs_count }} Errores
                                </span>
                            </div>
                            <p class="text-xs text-red-600 mt-2">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Ver detalles
                            </p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 2. SECCIÓN: RESUMEN DE CARTERA --}}
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-briefcase text-brand-dark mr-2"></i> Mi Cartera
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Clientes Totales -->
                <div class="bg-white rounded-xl shadow p-6 border-t-4 border-brand-dark">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-brand-dark">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase">Clientes Asignados</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_clients'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sincronizaciones Hoy -->
                <div class="bg-white rounded-xl shadow p-6 border-t-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase">Sincronizados Hoy</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['syncs_today'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Movimientos Totales -->
                <div class="bg-white rounded-xl shadow p-6 border-t-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-database fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase">Tx Procesadas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_tx'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. SECCIÓN: ACTIVIDAD RECIENTE --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-history mr-2 text-gray-400"></i> Última Actividad
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Hora</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Servicio</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Evento</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentActivity as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $log->happened_at->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $log->client->company ?? $log->client->fantasy_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $log->apiService->name ?? 'Sistema' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $log->event_type }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->status === 'success')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">OK</span>
                                    @elseif($log->status === 'error')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Error</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $log->status }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    No hay actividad reciente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-app-layout>