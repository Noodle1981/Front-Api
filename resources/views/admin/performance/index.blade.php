@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Métricas de Rendimiento</h2>
        <button onclick="window.location.href='{{ route('admin.performance.export') }}'"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Exportar Métricas
        </button>
    </div>
            <!-- Métricas de Conversión -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Métricas de Conversión</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600">Tasa de Conversión de Leads</div>
                        <div class="text-2xl font-bold">{{ number_format($conversionMetrics['lead_conversion_rate'], 1) }}%</div>
                        <div class="text-xs text-gray-500">
                            {{ $conversionMetrics['converted_leads'] }} de {{ $conversionMetrics['total_leads'] }} leads
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600">Tasa de Cierre de Deals</div>
                        <div class="text-2xl font-bold">{{ number_format($conversionMetrics['deal_win_rate'], 1) }}%</div>
                        <div class="text-xs text-gray-500">
                            {{ $conversionMetrics['won_deals'] }} de {{ $conversionMetrics['total_deals'] }} deals
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rendimiento por Usuario -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rendimiento por Usuario</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clientes</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deals</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tasa de Conversión</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($userPerformance as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->clients_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->deals_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ${{ number_format($user->total_value) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($user->conversion_rate, 1) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Métricas de Ventas -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Métricas de Ventas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600">Valor en Pipeline</div>
                        <div class="text-2xl font-bold">${{ number_format($salesMetrics['pipeline_value']) }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600">Tamaño Promedio de Deal</div>
                        <div class="text-2xl font-bold">${{ number_format($salesMetrics['average_deal_size']) }}</div>
                    </div>
                </div>
                
                <!-- Gráfico de Ventas Mensuales -->
                <div class="mt-4">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actividad Reciente</h3>
                @if($activityMetrics['recent_activities']->count() > 0)
                    <div class="space-y-4">
                        @foreach($activityMetrics['recent_activities']->take(10) as $activity)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-gray-100">
                                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">{{ $activity->description ?? $activity->type ?? 'Actividad' }}</p>
                                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No hay actividades recientes registradas.</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlySalesData = @json($salesMetrics['monthly_sales']);
    
    new Chart(document.getElementById('monthlySalesChart'), {
        type: 'bar',
        data: {
            labels: monthlySalesData.map(d => d.month),
            datasets: [{
                label: 'Valor Total de Ventas',
                data: monthlySalesData.map(d => d.value || 0),
                backgroundColor: '#60A5FA'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush