<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
                <i class="fas fa-history mr-2 text-brand-dark"></i> Mis Pedidos de Workflows
            </h2>
            <a href="{{ route('operator.workflows.request') }}" 
               class="px-4 py-2 bg-brand-dark text-white text-sm font-bold rounded-lg hover:shadow-lg transition">
                <i class="fas fa-plus mr-2"></i> Nuevo Pedido
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3"></i>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Lista de Pedidos -->
            <div class="grid grid-cols-1 gap-6">
                @forelse($requests as $request)
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-8 
                        {{ $request->status === 'completed' ? 'border-green-500' : 
                           ($request->status === 'in_progress' ? 'border-blue-500' : 
                           ($request->status === 'rejected' ? 'border-red-500' : 'border-yellow-500')) }}
                        hover:shadow-xl transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center flex-wrap gap-2 mb-2">
                                        <h3 class="text-lg font-bold text-gray-900">{{ $request->title }}</h3>
                                        
                                        <!-- Badge de Estado -->
                                        @if($request->status === 'pending')
                                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-yellow-100 text-yellow-800 border border-yellow-200 uppercase">
                                                <i class="fas fa-clock mr-1"></i> Pendiente
                                            </span>
                                        @elseif($request->status === 'in_progress')
                                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-blue-100 text-blue-800 border border-blue-200 uppercase">
                                                <i class="fas fa-spinner mr-1"></i> En Progreso
                                            </span>
                                        @elseif($request->status === 'completed')
                                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-green-100 text-green-800 border border-green-200 uppercase">
                                                <i class="fas fa-check-double mr-1"></i> Completado
                                            </span>
                                        @elseif($request->status === 'rejected')
                                            <span class="px-2 py-0.5 text-xs font-bold rounded bg-red-100 text-red-800 border border-red-200 uppercase">
                                                <i class="fas fa-times mr-1"></i> Rechazado
                                            </span>
                                        @endif

                                        <!-- Badge de Prioridad -->
                                        <span class="px-2 py-0.5 text-xs font-bold rounded 
                                            {{ $request->priority === 'high' ? 'bg-red-50 text-red-700' : 
                                               ($request->priority === 'medium' ? 'bg-yellow-50 text-yellow-700' : 'bg-green-50 text-green-700') }}">
                                            @if($request->priority === 'high')
                                                <i class="fas fa-arrow-up mr-1"></i> Alta
                                            @elseif($request->priority === 'medium')
                                                <i class="fas fa-minus mr-1"></i> Media
                                            @else
                                                <i class="fas fa-arrow-down mr-1"></i> Baja
                                            @endif
                                        </span>
                                    </div>

                                    <div class="flex items-center text-xs text-gray-500 space-x-3 mb-3">
                                        <span class="flex items-center"><i class="fas fa-building mr-1"></i> {{ $request->client->company }}</span>
                                        @if($request->branch)
                                            <span class="flex items-center"><i class="fas fa-map-marker-alt mr-1"></i> {{ $request->branch->branch_name }}</span>
                                        @endif
                                        <span class="flex items-center"><i class="fas fa-project-diagram mr-1"></i> {{ ucfirst($request->workflow_type) }}</span>
                                        <span class="flex items-center"><i class="fas fa-calendar-alt mr-1"></i> {{ $request->created_at->format('d/m/Y H:i') }}</span>
                                    </div>

                                    <p class="text-sm text-gray-600 italic">"{{ $request->description }}"</p>

                                    @if($request->expected_date)
                                        <div class="mt-2 flex items-center text-xs text-orange-600">
                                            <i class="fas fa-calendar-check mr-1"></i>
                                            Fecha esperada: {{ \Carbon\Carbon::parse($request->expected_date)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-col items-end space-y-2">
                                    @if($request->status === 'completed' && $request->batch)
                                        @php
                                            $execution = $request->batch->executions->first();
                                        @endphp
                                        @if($execution && $execution->status === 'success')
                                            <a href="{{ route('programmer.workflows.execution.pdf.download', $execution) }}" 
                                               class="px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition flex items-center">
                                                <i class="fas fa-file-pdf mr-2"></i> Descargar PDF
                                            </a>
                                            <a href="{{ route('programmer.workflows.execution.pdf.preview', $execution) }}" 
                                               target="_blank"
                                               class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition flex items-center">
                                                <i class="fas fa-eye mr-2"></i> Ver Resultado
                                            </a>
                                        @endif
                                    @elseif($request->status === 'pending')
                                        <span class="text-xs text-gray-500 italic">Esperando revisión...</span>
                                    @elseif($request->status === 'in_progress')
                                        <span class="text-xs text-blue-600 italic flex items-center">
                                            <i class="fas fa-cog fa-spin mr-2"></i> Siendo procesado...
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="text-xs text-red-600 italic">Pedido rechazado</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-12 border border-white/20 text-center">
                        <div class="h-20 w-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="fas fa-inbox text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">No has enviado pedidos aún</h3>
                        <p class="text-gray-500 mt-2 max-w-sm mx-auto">
                            Cuando solicites workflows, aparecerán aquí para que puedas hacer seguimiento.
                        </p>
                        <a href="{{ route('operator.workflows.request') }}" 
                           class="mt-4 inline-block px-6 py-2 bg-brand-dark text-white text-sm font-bold rounded-lg hover:shadow-lg transition">
                            <i class="fas fa-plus mr-2"></i> Solicitar Primer Workflow
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
