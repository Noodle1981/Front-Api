@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Estadísticas del Sistema</h2>
    </div>
            <!-- Métricas por Período -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Métricas por Período</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach(['daily' => 'Hoy', 'weekly' => 'Esta Semana', 'monthly' => 'Este Mes'] as $key => $title)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-lg mb-3">{{ $title }}</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nuevos Deals:</span>
                                        <span class="font-semibold">{{ $periodMetrics[$key]['new_deals'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Deals Ganados:</span>
                                        <span class="font-semibold text-green-600">{{ $periodMetrics[$key]['won_deals'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nuevos Leads:</span>
                                        <span class="font-semibold">{{ $periodMetrics[$key]['new_leads'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Leads Convertidos:</span>
                                        <span class="font-semibold text-blue-600">{{ $periodMetrics[$key]['converted_leads'] }}</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-2 mt-2">
                                        <span class="text-gray-600">Ingresos:</span>
                                        <span class="font-semibold text-green-600">${{ number_format($periodMetrics[$key]['revenue']) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Métricas por Usuario -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Rendimiento por Usuario</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Deals</th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deals Ganados</th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Leads</th>
                                    <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Leads Convertidos</th>
                                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($userMetrics as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ $user->total_deals }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ $user->won_deals }}
                                        <span class="text-xs text-gray-400">
                                            ({{ $user->total_deals ? round(($user->won_deals / $user->total_deals) * 100) : 0 }}%)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ $user->total_leads }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ $user->converted_leads }}
                                        <span class="text-xs text-gray-400">
                                            ({{ $user->total_leads ? round(($user->converted_leads / $user->total_leads) * 100) : 0 }}%)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                        ${{ number_format($user->revenue) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection