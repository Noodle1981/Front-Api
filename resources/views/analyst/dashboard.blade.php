<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-user-shield mr-2 text-brand-dark"></i> Centro de Comando - Inspector
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Executive Summary Panel --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @if($stats['system_health'] === 'healthy')
                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-2xl text-green-600"></i>
                            </div>
                        @elseif($stats['system_health'] === 'warning')
                            <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center mr-4">
                                <i class="fas fa-exclamation-triangle text-2xl text-yellow-600"></i>
                            </div>
                        @else
                            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
                                <i class="fas fa-times-circle text-2xl text-red-600"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Estado del Sistema</h3>
                            <p class="text-sm text-gray-600">
                                Tasa de error: {{ $stats['error_rate'] }}% | 
                                Tendencia: 
                                @if($stats['trend'] === 'improving')
                                    <span class="text-green-600"><i class="fas fa-arrow-down"></i> Mejorando</span>
                                @else
                                    <span class="text-red-600"><i class="fas fa-arrow-up"></i> Requiere atención</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ count($alerts) }}</div>
                        <div class="text-xs text-gray-500 uppercase">Alertas Activas</div>
                    </div>
                </div>
            </div>

            {{-- Alerts Panel --}}
            @if(count($alerts) > 0)
                <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bell mr-2 text-brand-dark"></i> Alertas y Notificaciones
                    </h3>
                    <div class="space-y-3">
                        @foreach($alerts as $alert)
                            <div class="flex items-start p-3 rounded-lg border-l-4 
                                @if($alert['level'] === 'critical') bg-red-50 border-red-500
                                @elseif($alert['level'] === 'warning') bg-yellow-50 border-yellow-500
                                @else bg-blue-50 border-blue-500 @endif">
                                <i class="fas {{ $alert['icon'] }} text-xl mr-3 mt-1
                                    @if($alert['level'] === 'critical') text-red-600
                                    @elseif($alert['level'] === 'warning') text-yellow-600
                                    @else text-blue-600 @endif"></i>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $alert['message'] }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ implode(', ', $alert['users']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-50/70 to-blue-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-blue-200/50 hover:from-blue-50/90 hover:to-blue-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-200 text-blue-700 mr-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-700 uppercase">Contadores</p>
                            <p class="text-3xl font-bold text-blue-900">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-indigo-50/70 to-indigo-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-indigo-200/50 hover:from-indigo-50/90 hover:to-indigo-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-200 text-indigo-700 mr-4">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-indigo-700 uppercase">Clientes</p>
                            <p class="text-3xl font-bold text-indigo-900">{{ $stats['total_clients'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-50/70 to-red-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-red-200/50 hover:from-red-50/90 hover:to-red-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-200 text-red-700 mr-4">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-700 uppercase">Errores Hoy</p>
                            <p class="text-3xl font-bold text-red-900">{{ $stats['errors_today'] }}</p>
                        </div>
                    </div>
                </div>
                 <div class="bg-gradient-to-br from-green-50/70 to-green-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-green-200/50 hover:from-green-50/90 hover:to-green-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-200 text-green-700 mr-4">
                            <i class="fas fa-exchange-alt text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-700 uppercase">Transacciones</p>
                            <p class="text-3xl font-bold text-green-900">{{ $stats['transactions_today'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Error Trend Chart --}}
                <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-brand-dark"></i> Tendencia de Errores (7 días)
                    </h3>
                    <canvas id="errorTrendChart" height="200"></canvas>
                </div>

                {{-- Rankings --}}
                <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-trophy mr-2 text-brand-dark"></i> Rankings
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">🏆 Más Eficientes (Menor Error)</p>
                            @foreach($rankings['most_efficient'] as $index => $user)
                                <div class="flex items-center justify-between text-sm py-1">
                                    <span class="text-gray-700">{{ $index + 1 }}. {{ $user->name }}</span>
                                    <span class="text-green-600 font-bold">{{ $user->error_rate }}%</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="border-t pt-3">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">🤖 Más Automatizados</p>
                            @foreach($rankings['most_automated'] as $index => $user)
                                <div class="flex items-center justify-between text-sm py-1">
                                    <span class="text-gray-700">{{ $index + 1 }}. {{ $user->name }}</span>
                                    <span class="text-blue-600 font-bold">{{ $user->automation_percent }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Users Table with Enhanced Metrics --}}
            <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg border border-white/20">
                <div class="p-6 bg-white/50 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Contadores - Vista Detallada</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clientes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa Error</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Automatización</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actividad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carga</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/30 divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-white/60 transition {{ $user->has_alerts ? 'bg-yellow-50/50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <span class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-700 font-bold text-lg">
                                                    {{ substr($user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                            @if($user->has_alerts)
                                                <i class="fas fa-exclamation-circle text-yellow-500 ml-2" title="Requiere atención"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $user->clients_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-bold {{ $user->error_rate > 10 ? 'text-red-600' : ($user->error_rate > 5 ? 'text-yellow-600' : 'text-green-600') }}">
                                                {{ $user->error_rate }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $user->automation_percent }}%"></div>
                                            </div>
                                            <span class="text-xs font-semibold text-gray-700">{{ $user->automation_percent }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($user->last_activity)
                                            <span class="{{ $user->days_inactive > 7 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                                {{ $user->last_activity->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">Sin actividad</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->workload === 'low')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baja</span>
                                        @elseif($user->workload === 'medium')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Media</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Alta</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('analyst.api-dashboard', ['user_filter' => $user->id]) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition" title="Ver Dashboard API">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <a href="{{ route('analyst.clients.index', ['user_filter' => $user->id]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 transition" title="Ver Clientes">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        console.log('🚀 Dashboard script loading...');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOM loaded');
            const errorCtx = document.getElementById('errorTrendChart');
            console.log('Canvas element:', errorCtx);
            if (errorCtx) {
                // Test with hardcoded data first
                const labels = ['12/12', '13/12', '14/12', '15/12', '16/12', '17/12', '18/12'];
                const data = [3, 7, 2, 5, 8, 4, 6];
                
                console.log('📊 Chart Labels:', labels);
                console.log('📊 Chart Data:', data);
                
                // Real data from PHP (for debugging)
                console.log('PHP Labels:', {!! json_encode($errorTrend['labels'] ?? []) !!});
                console.log('PHP Data:', {!! json_encode($errorTrend['data'] ?? []) !!});
                
                new Chart(errorCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Errores',
                    data: data,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
                });
                console.log('✅ Chart created successfully!');
            } else {
                console.error('❌ Canvas not found!');
            }
        });
    </script>
</x-app-layout>
