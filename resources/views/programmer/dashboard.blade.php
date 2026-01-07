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
                                Tasa de fallo: {{ $stats['error_rate'] }}% | 
                                Tendencia: 
                                @if($stats['trend'] === 'improving')
                                    <span class="text-green-600"><i class="fas fa-arrow-down"></i> Mejorando</span>
                                @else
                                    <span class="text-red-600"><i class="fas fa-arrow-up"></i> Requiere atención</span>
                                @endif
                            </p>
                        </div>
                    </div>

                </div>
            </div>



            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

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
                            <i class="fas fa-file-pdf text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-700 uppercase">Informes PDF</p>
                            <p class="text-3xl font-bold text-red-900">{{ $stats['pdf_reports'] }}</p>
                        </div>
                    </div>
                </div>

            </div>



            {{-- Recent Workflows Widget --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg border border-white/20">
                <div class="p-6 border-b border-gray-200/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-stream mr-2 text-brand-dark"></i> Workflows Recientes
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workflow</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentBatches as $batch)
                                    <tr class="hover:bg-white/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ $batch->batch_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $batch->workflowType->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $batch->client->company ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-full 
                                                {{ $batch->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $batch->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $batch->status === 'validated' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $batch->status === 'executing' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $batch->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ strtoupper($batch->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('programmer.workflows.batch.show', $batch) }}" class="text-brand-dark hover:underline font-bold">Ver</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                            No hay workflows recientes cargados.
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
