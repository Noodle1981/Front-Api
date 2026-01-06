<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings text-3xl font-extrabold text-primary-light tracking-tight drop-shadow mb-2">
            Centro de Gestión - Operador
        </h2>
    </x-slot>

    <div class="bg-background min-h-screen w-full p-4 md:p-6 space-y-8">

        {{-- 1. SECCIÓN: ATENCIÓN REQUERIDA --}}
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-bell text-yellow-500 mr-2"></i> Atención Requerida
            </h3>
            
            @if($recentlyCompleted)
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r shadow-sm flex items-center animate-pulse">
                    <div class="flex-shrink-0 text-blue-500">
                        <i class="fas fa-info-circle fa-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-bold text-blue-800 uppercase tracking-tight">¡Atención técnicos!</p>
                        <p class="text-sm text-blue-700">Workflows solicitados ya están habilitados y ejecutados. Revisa el historial para descargar los resultados.</p>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-center">
                    <div class="flex-shrink-0 text-green-500">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">Todo en orden. No hay alertas críticas en tus clientes hoy.</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- 2. SECCIÓN: RESUMEN DE CARTERA --}}
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-briefcase text-brand-dark mr-2"></i> Mi Cartera
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Clientes Totales -->
                <div class="bg-white rounded-xl shadow p-6 border-t-4 border-brand-dark hover:shadow-lg transition">
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

                <!-- Workflows Solicitados -->
                <div class="bg-white rounded-xl shadow p-6 border-t-4 border-amber-500 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-100 text-amber-600">
                            <i class="fas fa-paper-plane fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase font-bold">Workflows Solicitados</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['requested_workflows'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Workflows Ejecutados -->
                <div class="bg-white rounded-xl shadow p-6 border-t-4 border-purple-500 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase font-bold">Workflows Ejecutados</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['executed_workflows'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- PDF Descargados -->
                <div class="bg-white rounded-xl shadow p-6 border-t-4 border-red-500 hover:shadow-lg transition">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600">
                            <i class="fas fa-file-pdf fa-2x"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 uppercase font-bold">PDF Descargados</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['pdf_downloaded'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. SECCIÓN: ACTIVIDAD RECIENTE --}}
        <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-history mr-2 text-brand-dark opacity-70"></i> Última Actividad de Workflows
                </h3>
                <a href="{{ route('operator.workflows.history') }}" class="text-sm font-bold text-brand-dark hover:underline uppercase tracking-tight">Ver Todo</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100/50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Workflow</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentActivity as $batch)
                            <tr class="hover:bg-gray-50/80 transition shadow-sm">
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                    {{ $batch->uploaded_at->format('d/m H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">
                                    {{ $batch->client->company ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold">{{ $batch->workflowType->name }}</span>
                                </td>
                                <td class="px-6 py-4 border-none">
                                    <span class="px-3 py-1 text-[10px] font-bold rounded-full 
                                        {{ $batch->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $batch->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $batch->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $batch->status === 'processing' ? 'bg-blue-100 text-blue-800 animate-pulse' : '' }} uppercase tracking-wider">
                                        {{ $batch->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($batch->status === 'completed')
                                        <a href="#" class="inline-flex items-center text-red-600 hover:text-red-800 font-bold text-xs uppercase transition">
                                            <i class="fas fa-download mr-1.5 shadow-sm"></i> PDF
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic text-[10px]">Sin reporte</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                    <div class="opacity-30 mb-2"><i class="fas fa-inbox fa-3x"></i></div>
                                    No hay actividad de workflows registrada para tus clientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
