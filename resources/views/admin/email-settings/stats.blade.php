<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-chart-bar mr-2"></i> {{ __('Estadísticas de Emails') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-50/70 to-blue-100/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-blue-200/50 hover:from-blue-50/90 hover:to-blue-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-200 text-blue-700 mr-4">
                            <i class="fas fa-envelope text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-700 uppercase">Total Enviados</p>
                            <p class="text-3xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50/70 to-green-100/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-green-200/50 hover:from-green-50/90 hover:to-green-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-200 text-green-700 mr-4">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-700 uppercase">Exitosos</p>
                            <p class="text-3xl font-bold text-green-900">{{ $stats['sent'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-red-50/70 to-red-100/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-red-200/50 hover:from-red-50/90 hover:to-red-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-200 text-red-700 mr-4">
                            <i class="fas fa-times-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-700 uppercase">Fallidos</p>
                            <p class="text-3xl font-bold text-red-900">{{ $stats['failed'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50/70 to-purple-100/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-purple-200/50 hover:from-purple-50/90 hover:to-purple-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-200 text-purple-700 mr-4">
                            <i class="fas fa-percentage text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-700 uppercase">Tasa de Éxito</p>
                            <p class="text-3xl font-bold text-purple-900">{{ $stats['success_rate'] }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Daily Trend Chart --}}
                <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20 hover:bg-white/80 transition-all">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-chart-line mr-2 text-brand-dark"></i> Tendencia Diaria (30 días)
                    </h3>
                    <div style="height: 250px;">
                        <canvas id="dailyTrendChart"></canvas>
                    </div>
                </div>

                {{-- Emails by Type Chart --}}
                <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20 hover:bg-white/80 transition-all">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-chart-pie mr-2 text-brand-dark"></i> Emails por Tipo
                    </h3>
                    <div style="height: 250px;">
                        <canvas id="emailTypeChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('admin.email.settings') }}" 
                   class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20 hover:bg-white/90 transition-all flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-cog text-2xl text-blue-500 mr-3"></i>
                        <div>
                            <h4 class="font-bold text-gray-800">Configuración</h4>
                            <p class="text-xs text-gray-600">Enviar email de prueba</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a href="{{ route('admin.email.history') }}" 
                   class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20 hover:bg-white/90 transition-all flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-history text-2xl text-purple-500 mr-3"></i>
                        <div>
                            <h4 class="font-bold text-gray-800">Historial</h4>
                            <p class="text-xs text-gray-600">Ver todos los emails</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Trend Chart
            const dailyCtx = document.getElementById('dailyTrendChart');
            if (dailyCtx) {
                new Chart(dailyCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($stats['daily_trend']['labels']) !!},
                        datasets: [
                            {
                                label: 'Enviados',
                                data: {!! json_encode($stats['daily_trend']['sent']) !!},
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Fallidos',
                                data: {!! json_encode($stats['daily_trend']['failed']) !!},
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
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
                    }
                });
            }

            // Email Type Chart
            const typeCtx = document.getElementById('emailTypeChart');
            if (typeCtx) {
                const typeData = @json($stats['by_type']);
                new Chart(typeCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(typeData).map(key => {
                            return key === 'test' ? 'Prueba' : 
                                   key === 'api_error' ? 'Error API' : 
                                   'Sistema';
                        }),
                        datasets: [{
                            data: Object.values(typeData),
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(156, 163, 175, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
