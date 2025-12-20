<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="font-headings font-bold text-2xl text-gray-800 leading-tight">
                    <i class="fas fa-tower-broadcast mr-2 text-aurora-cyan"></i> {{ __('Monitor de APIs & Endpoints') }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">Monitoreo en tiempo real de todas tus integraciones</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('programmer.enterprise.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm text-gray-700 rounded-full font-semibold text-sm hover:bg-white transition border border-gray-200">
                    <i class="fas fa-plug mr-2"></i> Gestionar APIs
                </a>
                <a href="{{ route('programmer.business-rules.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-btn-start to-btn-end text-white rounded-full font-bold text-sm shadow transition hover:scale-105">
                    <i class="fas fa-code-branch mr-2"></i> Reglas ETL
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- SECCIÓN 1: KPIs PRINCIPALES --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                {{-- Errores Hoy --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-red-50/80 to-white/70 rounded-2xl shadow-lg p-5 border border-red-200/30 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-red-600 uppercase tracking-wider">Errores Hoy</p>
                            <p class="text-3xl font-bold text-red-700 mt-1">{{ $stats['errors_today'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Sincronizaciones OK --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-green-50/80 to-white/70 rounded-2xl shadow-lg p-5 border border-green-200/30 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-green-600 uppercase tracking-wider">Syncs Hoy</p>
                            <p class="text-3xl font-bold text-green-700 mt-1">{{ $stats['syncs_today'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Clientes Conectados --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-blue-50/80 to-white/70 rounded-2xl shadow-lg p-5 border border-blue-200/30 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-blue-600 uppercase tracking-wider">Conectados</p>
                            <p class="text-3xl font-bold text-blue-700 mt-1">{{ $stats['connected_clients'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- APIs Automatizadas --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-purple-50/80 to-white/70 rounded-2xl shadow-lg p-5 border border-purple-200/30 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-purple-600 uppercase tracking-wider">Automatizadas</p>
                            <p class="text-3xl font-bold text-purple-700 mt-1">{{ $stats['automated_apis'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>

                {{-- Total APIs --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-gray-50/80 to-white/70 rounded-2xl shadow-lg p-5 border border-gray-200/30 hover:shadow-xl transition-all">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wider">Total APIs</p>
                            <p class="text-3xl font-bold text-gray-700 mt-1">{{ $stats['total_apis'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-plug text-gray-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: GRÁFICOS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Gráfico de Actividad --}}
                <div class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg p-6 border border-white/30">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-800">
                            <i class="fas fa-chart-area mr-2 text-aurora-cyan"></i> Actividad de APIs
                        </h3>
                        <div class="flex space-x-2 text-xs">
                            @foreach(['7d' => '7D', '30d' => '30D', '90d' => '90D'] as $key => $label)
                                <a href="{{ route('programmer.api-dashboard', ['range' => $key]) }}" 
                                   class="px-3 py-1 rounded-full transition {{ request('range', '7d') === $key ? 'bg-aurora-cyan text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>

                {{-- Gráfico de Distribución por Servicio --}}
                <div class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg p-6 border border-white/30">
                    <h3 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-chart-pie mr-2 text-aurora-cyan"></i> Distribución por Servicio
                    </h3>
                    <div class="h-64 flex justify-center">
                        <canvas id="servicesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 3: BITÁCORA EN VIVO --}}
            <div x-data="{ showModal: false, modalContent: '' }" class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg p-6 border border-white/30">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800">
                        <i class="fas fa-satellite-dish mr-2 text-aurora-cyan animate-pulse"></i> Bitácora en Vivo
                    </h3>
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                        <i class="fas fa-circle text-xs mr-1 animate-pulse"></i> En tiempo real
                    </span>
                </div>
                
                {{-- Filtros --}}
                <form method="GET" action="{{ route('programmer.api-dashboard') }}" class="mb-4 backdrop-blur-sm bg-gray-50/80 p-4 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cliente</label>
                            <select name="client_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-aurora-cyan focus:border-aurora-cyan">
                                <option value="">Todos</option>
                                @foreach($filterClients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Servicio API</label>
                            <select name="service_id" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-aurora-cyan focus:border-aurora-cyan">
                                <option value="">Todos</option>
                                @foreach($filterServices as $service)
                                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                            <select name="status" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-aurora-cyan focus:border-aurora-cyan">
                                <option value="">Todos</option>
                                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>✅ Exitoso</option>
                                <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>❌ Error</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition text-sm font-semibold">
                                <i class="fas fa-filter mr-1"></i> Filtrar
                            </button>
                            <a href="{{ route('programmer.api-dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <input type="hidden" name="range" value="{{ request('range', '7d') }}">
                </form>
                
                {{-- Tabla de Logs --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-600 uppercase bg-gray-50/80">
                            <tr>
                                <th class="px-4 py-3 text-left">Hora</th>
                                <th class="px-4 py-3 text-left">Evento</th>
                                <th class="px-4 py-3 text-left">Cliente</th>
                                <th class="px-4 py-3 text-left">Servicio</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-center">Detalle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-4 py-3 font-mono text-gray-500 text-xs">
                                        {{ $log->happened_at->format('d/m H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $log->event_type }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="text-gray-700">{{ $log->client->company ?? 'Sistema' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                            {{ $log->apiService->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($log->status === 'success')
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">OK</span>
                                        @elseif($log->status === 'error')
                                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">ERROR</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">{{ $log->status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($log->details)
                                            <button @click="showModal = true; modalContent = '{{ addslashes($log->details) }}'" 
                                                class="text-blue-600 hover:text-blue-800 text-xs font-bold">
                                                <i class="fas fa-code"></i> JSON
                                            </button>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                        <p>No hay registros de actividad</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($logs->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100 mt-4">
                        {{ $logs->links() }}
                    </div>
                @endif

                {{-- Modal JSON --}}
                <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4">
                        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showModal = false"></div>
                        <div class="relative backdrop-blur-xl bg-white/90 rounded-2xl shadow-2xl max-w-lg w-full p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">
                                <i class="fas fa-code mr-2 text-aurora-cyan"></i> Detalle JSON
                            </h3>
                            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg text-xs overflow-auto max-h-60 font-mono" x-text="JSON.stringify(JSON.parse(modalContent), null, 2)"></pre>
                            <button @click="showModal = false" class="mt-4 w-full px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition">
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Activity Chart
            const ctxActivity = document.getElementById('activityChart').getContext('2d');
            new Chart(ctxActivity, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartActivity['labels']) !!},
                    datasets: [
                        {
                            label: 'Exitosas',
                            data: {!! json_encode($chartActivity['success']) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.7)',
                            borderRadius: 4
                        },
                        {
                            label: 'Errores',
                            data: {!! json_encode($chartActivity['error']) !!},
                            backgroundColor: 'rgba(239, 68, 68, 0.7)',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });

            // Services Chart
            const ctxServices = document.getElementById('servicesChart').getContext('2d');
            new Chart(ctxServices, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartServices['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartServices['data']) !!},
                        backgroundColor: ['#3B82F6', '#F59E0B', '#10B981', '#6366F1', '#EC4899', '#14B8A6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right' } }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
