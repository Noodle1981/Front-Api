<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Estadísticas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Resumen General -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">Clientes Activos</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['clients_count'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">Deals Activos</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['active_deals'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">Valor Total Cerrado</div>
                    <div class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_value']) }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 mb-1">Leads Activos</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['leads_count'] }}</div>
                </div>
            </div>

            <!-- Rendimiento Mensual -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rendimiento Mensual</h3>
                <canvas id="monthlyChart" class="w-full" height="300"></canvas>
            </div>

            <!-- Conversión de Leads -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Conversión de Leads</h3>
                <div class="flex items-center">
                    <div class="flex-1">
                        <div class="relative pt-1">
                            <div class="flex mb-2 items-center justify-between">
                                <div>
                                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                        Tasa de Conversión
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-semibold inline-block text-blue-600">
                                        {{ $leadStats->total > 0 ? number_format(($leadStats->converted / $leadStats->total) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                            </div>
                            <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                                <div style="width:{{ $leadStats->total > 0 ? ($leadStats->converted / $leadStats->total) * 100 : 0 }}%" 
                                     class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actividad Reciente</h3>
                <div class="space-y-4">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const monthlyData = @json($monthlyPerformance);
        
        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [{
                    label: 'Valor Total Cerrado',
                    data: monthlyData.map(d => d.closed_value),
                    backgroundColor: '#60A5FA',
                    yAxisID: 'y'
                }, {
                    label: 'Número de Deals',
                    data: monthlyData.map(d => d.total_deals),
                    backgroundColor: '#93C5FD',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Valor ($)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Número de Deals'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>