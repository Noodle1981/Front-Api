<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-user-shield mr-2 text-brand-dark"></i> Centro de Comando - Programador
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
                                Tasa de éxito: <span class="font-bold text-green-600">{{ $stats['success_rate'] }}%</span> | 
                                Ejecuciones: <span class="font-bold">{{ $stats['total_executions'] }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Tiempo promedio de ejecución</p>
                        <p class="text-2xl font-bold text-brand-dark">{{ $stats['avg_execution_time'] }}s</p>
                    </div>
                </div>
            </div>

            {{-- Stats Cards - Row 1 --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Clientes --}}
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

                {{-- Ejecuciones --}}
                <div class="bg-gradient-to-br from-purple-50/70 to-purple-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-purple-200/50 hover:from-purple-50/90 hover:to-purple-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-200 text-purple-700 mr-4">
                            <i class="fas fa-play-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-purple-700 uppercase">Ejecuciones</p>
                            <p class="text-3xl font-bold text-purple-900">{{ $stats['total_executions'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Tickets Procesados --}}
                <div class="bg-gradient-to-br from-green-50/70 to-green-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-green-200/50 hover:from-green-50/90 hover:to-green-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-200 text-green-700 mr-4">
                            <i class="fas fa-receipt text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-700 uppercase">Tickets</p>
                            <p class="text-3xl font-bold text-green-900">{{ number_format($stats['total_tickets']) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Comensales --}}
                <div class="bg-gradient-to-br from-orange-50/70 to-orange-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-orange-200/50 hover:from-orange-50/90 hover:to-orange-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-200 text-orange-700 mr-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-orange-700 uppercase">Comensales</p>
                            <p class="text-3xl font-bold text-orange-900">{{ number_format($stats['total_comensales']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ventas Totales Card --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 shadow-lg rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm uppercase font-medium">Ventas Totales Procesadas</p>
                        <p class="text-4xl font-bold mt-1">${{ number_format($stats['total_ventas'], 2, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/20 rounded-full p-4">
                        <i class="fas fa-dollar-sign text-4xl"></i>
                    </div>
                </div>
            </div>

            {{-- Conciliación Panel --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-pie mr-2 text-brand-dark"></i> Porcentaje de Conciliación Promedio
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Mercado Pago --}}
                    <div class="text-center p-4 rounded-lg bg-blue-50">
                        <p class="text-xs text-blue-600 uppercase font-medium">Mercado Pago</p>
                        <p class="text-3xl font-bold text-blue-800">{{ $conciliacion['mercado_pago'] }}%</p>
                        <div class="w-full bg-blue-200 rounded-full h-2 mt-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($conciliacion['mercado_pago'], 100) }}%"></div>
                        </div>
                    </div>

                    {{-- Getnet --}}
                    <div class="text-center p-4 rounded-lg bg-purple-50">
                        <p class="text-xs text-purple-600 uppercase font-medium">Getnet</p>
                        <p class="text-3xl font-bold text-purple-800">{{ $conciliacion['getnet'] }}%</p>
                        <div class="w-full bg-purple-200 rounded-full h-2 mt-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min($conciliacion['getnet'], 100) }}%"></div>
                        </div>
                    </div>

                    {{-- Efectivo --}}
                    <div class="text-center p-4 rounded-lg bg-green-50">
                        <p class="text-xs text-green-600 uppercase font-medium">Efectivo</p>
                        <p class="text-3xl font-bold text-green-800">{{ $conciliacion['efectivo'] }}%</p>
                        <div class="w-full bg-green-200 rounded-full h-2 mt-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($conciliacion['efectivo'], 100) }}%"></div>
                        </div>
                    </div>

                    {{-- Promedio General --}}
                    <div class="text-center p-4 rounded-lg bg-gradient-to-br from-brand-dark/10 to-brand-dark/20">
                        <p class="text-xs text-brand-dark uppercase font-medium">Promedio General</p>
                        <p class="text-3xl font-bold text-brand-dark">{{ $conciliacion['promedio'] }}%</p>
                        <div class="w-full bg-brand-dark/30 rounded-full h-2 mt-2">
                            <div class="bg-brand-dark h-2 rounded-full" style="width: {{ min($conciliacion['promedio'], 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Executions Widget --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg border border-white/20">
                <div class="p-6 border-b border-gray-200/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-stream mr-2 text-brand-dark"></i> Ejecuciones Recientes
                    </h3>
                    <div class="flex space-x-2">
                        <a href="{{ route('programmer.workflows.upload') }}" class="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition">
                            <i class="fas fa-plus mr-1"></i> NUEVO
                        </a>
                        <a href="{{ route('programmer.workflows.history') }}" class="px-3 py-1 bg-brand-dark text-white text-xs font-bold rounded hover:bg-opacity-90 transition">
                            VER TODO
                        </a>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workflow</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tiempo</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentExecutions as $execution)
                                    <tr class="hover:bg-white/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ $execution->fileBatch->client->company ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $execution->fileBatch->workflowType->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $execution->completed_at?->format('d/m/Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ round(($execution->execution_time_ms ?? 0) / 1000, 2) }}s
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('execution.pdf.preview', $execution) }}" class="text-brand-dark hover:underline font-bold">
                                                <i class="fas fa-file-pdf mr-1"></i>Ver PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                            No hay ejecuciones recientes. Los datos aparecerán cuando se ejecuten workflows.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
