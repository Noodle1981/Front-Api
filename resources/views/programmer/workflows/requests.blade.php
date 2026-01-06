<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
                <i class="fas fa-envelope-open-text mr-2 text-brand-dark"></i> Gestión de Pedidos de Workflows
            </h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full uppercase">
                    {{ $requests->count() }} Solicitudes
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Filtros y Barra de Búsqueda --}}
            <div class="bg-white/80 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Buscar pedidos..." class="pl-10 pr-4 py-2 border-gray-300 rounded-lg text-sm focus:ring-brand-accent focus:border-brand-accent w-64">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="flex bg-gray-100 p-1 rounded-lg border border-gray-200">
                        <button class="px-3 py-1.5 bg-white shadow-sm text-xs font-bold rounded-md text-gray-800">TODO</button>
                        <button class="px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700">PENDIENTE</button>
                        <button class="px-3 py-1.5 text-xs font-bold text-gray-500 hover:text-gray-700">ACEPTADO</button>
                    </div>
                </div>
            </div>

            {{-- Lista de Pedidos --}}
            <div class="grid grid-cols-1 gap-6">
                @forelse($requests as $request)
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-l-8 
                        {{ $request->priority === 'high' ? 'border-red-500' : ($request->priority === 'medium' ? 'border-yellow-500' : 'border-green-500') }}
                        hover:shadow-xl transition-shadow">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-start space-x-4">
                                    <div class="p-3 rounded-lg 
                                        {{ $request->priority === 'high' ? 'bg-red-50 text-red-600' : ($request->priority === 'medium' ? 'bg-yellow-50 text-yellow-600' : 'bg-green-50 text-green-600') }}">
                                        @if($request->priority === 'high')
                                            <i class="fas fa-arrow-up text-xl"></i>
                                        @elseif($request->priority === 'medium')
                                            <i class="fas fa-minus text-xl"></i>
                                        @else
                                            <i class="fas fa-arrow-down text-xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center flex-wrap gap-2">
                                            <h3 class="text-lg font-bold text-gray-900">{{ $request->title }}</h3>
                                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wider">
                                                <i class="fas fa-project-diagram mr-1"></i> {{ $request->workflow_type }}
                                            </span>
                                            @if($request->status === 'in_progress')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-green-100 text-green-800 border border-green-200 uppercase tracking-wider">
                                                    <i class="fas fa-check mr-1"></i> ACEPTADO
                                                </span>
                                            @elseif($request->status === 'completed')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-blue-100 text-blue-800 border border-blue-200 uppercase tracking-wider">
                                                    <i class="fas fa-check-double mr-1"></i> COMPLETADO
                                                </span>
                                            @elseif($request->status === 'rejected')
                                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-800 border border-red-200 uppercase tracking-wider">
                                                    <i class="fas fa-times mr-1"></i> RECHAZADO
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center text-xs text-gray-500 mt-1 space-x-3">
                                            <span class="flex items-center"><i class="fas fa-user-edit mr-1"></i> {{ $request->user->name }}</span>
                                            <span class="flex items-center"><i class="fas fa-building mr-1"></i> {{ $request->client->company }}</span>
                                            @if($request->branch)
                                                <span class="flex items-center"><i class="fas fa-map-marker-alt mr-1"></i> {{ $request->branch->branch_name }}</span>
                                            @endif
                                            <span class="flex items-center"><i class="fas fa-calendar-alt mr-1"></i> Solicitado el {{ $request->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    @if($request->status === 'pending')
                                        <form action="{{ route('programmer.workflows.requests.reject', $request) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-white text-gray-700 text-sm font-bold rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                                                RECHAZAR
                                            </button>
                                        </form>
                                        <form action="{{ route('programmer.workflows.requests.accept', $request) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-6 py-2 bg-brand-dark text-white text-sm font-bold rounded-lg hover:shadow-lg transition transform hover:-translate-y-0.5 active:translate-y-0">
                                                ACEPTAR Y EMPEZAR
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="px-6 py-2 bg-gray-100 text-gray-400 text-sm font-bold rounded-lg border border-gray-200 cursor-not-allowed uppercase">
                                            Procesado
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-align-left mr-2 text-brand-dark"></i> Descripción del requerimiento:
                                </p>
                                <p class="text-sm text-gray-600 leading-relaxed italic">
                                    "{{ $request->description }}"
                                </p>
                            </div>

                            @if($request->expected_date)
                                <div class="mt-4 flex items-center text-sm font-bold text-red-600 bg-red-50 p-2 rounded border border-red-100 w-fit">
                                    <i class="fas fa-clock mr-2"></i> FECHA ESPERADA: {{ \Carbon\Carbon::parse($request->expected_date)->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-12 border border-white/20 text-center">
                        <div class="h-20 w-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="fas fa-inbox text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">No hay pedidos pendientes</h3>
                        <p class="text-gray-500 mt-2 max-w-sm mx-auto">
                            Cuando los operadores soliciten nuevos workflows, aparecerán aquí para que puedas gestionarlos.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{-- {{ $requests->links() }} --}}
        </div>
    </div>
</x-app-layout>
