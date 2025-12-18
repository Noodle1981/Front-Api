<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tower-broadcast mr-2 text-brand-dark"></i> Centro de Control de APIs
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- User Context Selector (Only for Analysts/Admins) --}}
            @if($contextUsers->isNotEmpty())
                <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-filter text-brand-dark mr-3 text-xl"></i>
                            <div>
                                <h3 class="text-sm font-bold text-gray-800">Vista de Contexto</h3>
                                <p class="text-xs text-gray-500">Filtrar dashboard por contador específico</p>
                            </div>
                        </div>
                        
                        <form method="GET" action="{{ route(request()->route()->getName()) }}" class="flex items-center gap-3">
                            <select name="user_filter" onchange="this.form.submit()" 
                                class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent bg-white/90">
                                <option value="">📊 General (Todos los contadores)</option>
                                @foreach($contextUsers as $contextUser)
                                    <option value="{{ $contextUser->id }}" {{ $selectedUser && $selectedUser->id == $contextUser->id ? 'selected' : '' }}>
                                        👤 {{ $contextUser->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            @if($selectedUser)
                                <a href="{{ route(request()->route()->getName()) }}" 
                                   class="px-3 py-2 text-xs bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                    <i class="fas fa-times mr-1"></i> Limpiar
                                </a>
                            @endif
                            
                            {{-- Mantener otros parámetros --}}
                            <input type="hidden" name="range" value="{{ request('range', '7d') }}">
                        </form>
                    </div>
                    
                    @if($selectedUser)
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center text-sm">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold mr-2">
                                    <i class="fas fa-eye mr-1"></i> Vista Filtrada
                                </span>
                                <span class="text-gray-600">Mostrando datos de: <strong class="text-gray-900">{{ $selectedUser->name }}</strong></span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            
            {{-- SECCIÓN 1: HEALTH CHECK (Tarjetas) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card: Errores Hoy -->
                <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border-l-4 border-red-500 hover:bg-white/80 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-500 mr-4">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Errores (Hoy)</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $stats['errors_today'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Sincronizaciones Exitosas -->
                <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border-l-4 border-green-500 hover:bg-white/80 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sincronizaciones (Hoy)</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $stats['syncs_today'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Clientes Conectados -->
                <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border-l-4 border-blue-500 hover:bg-white/80 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Clientes Conectados</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $stats['connected_clients'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN CHAGPTS (GRÁFICOS) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gráfico de Actividad -->
                <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-white/20">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800">
                            <i class="fas fa-chart-area mr-2"></i> Actividad
                        </h3>
                        <div class="flex space-x-2 text-xs">
                            @foreach(['7d' => '7 Días', '30d' => '30 Días', '90d' => 'Trimestre', '1y' => 'Año'] as $key => $label)
                                <a href="{{ route('api.dashboard', ['range' => $key]) }}" 
                                   class="px-2 py-1 rounded transition {{ request('range', '7d') === $key ? 'bg-brand-dark text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="h-64">
                         <canvas id="activityChart"></canvas>
                    </div>
                </div>

                <!-- Gráfico de Distribución -->
                <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-white/20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-chart-pie mr-2"></i> Uso por Servicio
                    </h3>
                    <div class="h-64 flex justify-center">
                         <canvas id="servicesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- SECCIÓN 2: BITÁCORA EN VIVO (Live Feed) --}}
                <div x-data="{ showModal: false, modalContent: '' }" class="lg:col-span-2 bg-white/70 backdrop-blur-md shadow-lg sm:rounded-lg p-6 relative border border-white/20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-list-ul mr-2"></i> Bitácora en Vivo
                    </h3>
                    
                    {{-- Filtros --}}
                    <form method="GET" action="{{ route(request()->route()->getName()) }}" class="mb-4 bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                            {{-- Filtro Cliente --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Cliente</label>
                                <select name="client_id" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                                    <option value="">Todos</option>
                                    @foreach($filterClients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->company }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro Usuario --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Usuario</label>
                                <select name="user_id" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                                    <option value="">Todos</option>
                                    @foreach($filterUsers as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro Servicio --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Servicio</label>
                                <select name="service_id" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                                    <option value="">Todos</option>
                                    @foreach($filterServices as $service)
                                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro Estado --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                                <select name="status" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                                    <option value="">Todos</option>
                                    <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Exitoso</option>
                                    <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Error</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 mt-3">
                            <a href="{{ route(request()->route()->getName()) }}" class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                                Limpiar
                            </a>
                            <button type="submit" class="px-3 py-1.5 text-sm bg-brand-dark text-white rounded hover:bg-gray-800 transition">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                        </div>
                        
                        {{-- Mantener el rango de tiempo --}}
                        <input type="hidden" name="range" value="{{ request('range', '7d') }}">
                    </form>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3">Hora</th>
                                    <th class="px-4 py-3">Evento</th>
                                    <th class="px-4 py-3">Cliente</th>
                                    <th class="px-4 py-3">Usuario</th>
                                    <th class="px-4 py-3">Servicio</th>
                                    <th class="px-4 py-3">Estado</th>
                                    <th class="px-4 py-3">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($logs as $log)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-gray-600">
                                            {{ $log->happened_at->format('H:i:s') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-900">
                                            {{ $log->event_type }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $log->client->company ?? 'Sistema' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($log->client && $log->client->user)
                                                <div class="flex items-center">
                                                    <div class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs mr-2">
                                                        {{ substr($log->client->user->name, 0, 1) }}
                                                    </div>
                                                    <span class="text-xs text-gray-700">{{ $log->client->user->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded border border-gray-200">
                                                {{ $log->apiService->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($log->status === 'success')
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">OK</span>
                                            @elseif($log->status === 'error')
                                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">ERROR</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $log->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($log->details)
                                                <button @click="showModal = true; modalContent = '{{ addslashes($log->details) }}'" 
                                                    class="text-blue-600 hover:text-blue-900 text-xs font-bold">
                                                    <i class="fas fa-code"></i> JSON
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            No hay registros de actividad hoy.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($logs->hasPages())
                        <div class="px-4 py-3 border-t border-gray-200">
                            {{ $logs->links() }}
                        </div>
                    @endif

                    {{-- MODAL JSON --}}
                    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                Detalle del Log (JSON)
                                            </h3>
                                            <div class="mt-2">
                                                <pre class="bg-gray-900 text-green-400 p-4 rounded text-xs overflow-auto max-h-60" x-text="JSON.stringify(JSON.parse(modalContent), null, 2)"></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- SECCIÓN 3: SERVICIOS Y ACCESOS RÁPIDOS --}}
                <div class="bg-white/70 backdrop-blur-md shadow-lg sm:rounded-lg p-6 h-fit border border-white/20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                        <i class="fas fa-cubes mr-2"></i> Servicios Activos
                    </h3>
                    
                    {{-- Estadísticas de Automatización --}}
                    <div class="space-y-3 mb-4">
                        <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <i class="fas fa-robot text-green-600 mr-3 text-xl"></i>
                                <div>
                                    <span class="text-gray-700 font-medium block">Automatizadas</span>
                                    <span class="text-xs text-gray-500">Con script/cron</span>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ $stats['automated_apis'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                            <div class="flex items-center">
                                <i class="fas fa-hand-pointer text-orange-600 mr-3 text-xl"></i>
                                <div>
                                    <span class="text-gray-700 font-medium block">Manuales</span>
                                    <span class="text-xs text-gray-500">Ejecución manual</span>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-orange-600">{{ $stats['manual_apis'] }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <i class="fas fa-plug text-blue-600 mr-3 text-xl"></i>
                                <div>
                                    <span class="text-gray-700 font-medium block">Total Activas</span>
                                    <span class="text-xs text-gray-500">Todas las conexiones</span>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-blue-600">{{ $stats['total_apis'] }}</span>
                        </div>
                    </div>
                    
                    {{-- Porcentaje de Automatización --}}
                    @if($stats['total_apis'] > 0)
                        <div class="bg-gray-50 p-3 rounded-lg mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-medium text-gray-600">Nivel de Automatización</span>
                                <span class="text-xs font-bold text-gray-800">{{ round(($stats['automated_apis'] / $stats['total_apis']) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ ($stats['automated_apis'] / $stats['total_apis']) * 100 }}%"></div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="{{ route('clients.index') }}" class="block w-full text-center px-4 py-2 bg-brand-dark text-white rounded hover:bg-gray-800 transition">
                            <i class="fas fa-arrow-right mr-2"></i> Ir a Clientes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Activity Chart
            const ctxActivity = document.getElementById('activityChart').getContext('2d');
            new Chart(ctxActivity, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartActivity['labels']) !!},
                    datasets: [
                        {
                            label: 'Exitosas',
                            data: {!! json_encode($chartActivity['success']) !!},
                            borderColor: '#10B981', // green-500
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderWidth: 1
                        },
                        {
                            label: 'Errores',
                            data: {!! json_encode($chartActivity['error']) !!},
                            borderColor: '#EF4444', // red-500
                            backgroundColor: 'rgba(239, 68, 68, 0.6)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });

            // 2. Services Chart
            const ctxServices = document.getElementById('servicesChart').getContext('2d');
            new Chart(ctxServices, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartServices['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartServices['data']) !!},
                        backgroundColor: [
                            '#3B82F6', '#F59E0B', '#10B981', '#6366F1', '#EC4899'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
