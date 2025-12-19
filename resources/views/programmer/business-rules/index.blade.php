<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <h2 class="font-headings font-bold text-2xl text-gray-800 leading-tight">
                    <i class="fas fa-code-branch mr-2 text-aurora-cyan"></i> {{ __('Reglas de Negocio ETL') }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">Gestiona tus reglas de Extracción, Transformación y Visualización</p>
            </div>
            <div class="flex items-center gap-4">
                {{-- Motor Python Status --}}
                <div class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                    <i class="fas fa-circle text-xs animate-pulse"></i>
                    <span>Motor Python <span class="font-bold">Ready</span></span>
                </div>
                <a href="{{ route('programmer.business-rules.create') }}" 
                   class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-btn-start to-btn-end hover:to-btn-start text-white border border-transparent rounded-full font-bold text-xs uppercase tracking-widest shadow-[0_4px_14px_0_rgba(247,131,143,0.39)] transition-all ease-in-out duration-300 transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i> Nueva Regla
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Estadísticas Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="backdrop-blur-xl bg-white/70 rounded-2xl shadow-lg p-6 border border-white/20 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Total Reglas</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-layer-group text-gray-500 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="backdrop-blur-xl bg-gradient-to-br from-blue-50/80 to-white/70 rounded-2xl shadow-lg p-6 border border-blue-200/30 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Extracción</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['extraction'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-download text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="backdrop-blur-xl bg-gradient-to-br from-purple-50/80 to-white/70 rounded-2xl shadow-lg p-6 border border-purple-200/30 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Transformación</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['transformation'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-purple-500 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="backdrop-blur-xl bg-gradient-to-br from-green-50/80 to-white/70 rounded-2xl shadow-lg p-6 border border-green-200/30 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Visualización</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['visualization'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-bar text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabla de Reglas --}}
            <div class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg border border-white/30 overflow-hidden">
                {{-- Filtros --}}
                <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-500 mr-2">Filtrar:</span>
                    <a href="{{ route('programmer.business-rules.index') }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition {{ !$type ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Todas
                    </a>
                    <a href="{{ route('programmer.business-rules.index', ['type' => 'extraction']) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition {{ $type === 'extraction' ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-600 hover:bg-blue-200' }}">
                        <i class="fas fa-download mr-1"></i> Extracción
                    </a>
                    <a href="{{ route('programmer.business-rules.index', ['type' => 'transformation']) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition {{ $type === 'transformation' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-600 hover:bg-purple-200' }}">
                        <i class="fas fa-exchange-alt mr-1"></i> Transformación
                    </a>
                    <a href="{{ route('programmer.business-rules.index', ['type' => 'visualization']) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition {{ $type === 'visualization' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-600 hover:bg-green-200' }}">
                        <i class="fas fa-chart-bar mr-1"></i> Visualización
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    @if($rules->count() > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($rules as $rule)
                                <div class="px-6 py-4 hover:bg-gray-50 transition">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="font-semibold text-gray-800">{{ $rule->name }}</h3>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $rule->type === 'extraction' ? 'bg-blue-100 text-blue-700' : '' }}
                                                    {{ $rule->type === 'transformation' ? 'bg-purple-100 text-purple-700' : '' }}
                                                    {{ $rule->type === 'visualization' ? 'bg-green-100 text-green-700' : '' }}">
                                                    {{ ucfirst($rule->type) }}
                                                </span>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $rule->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $rule->status === 'active' ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mb-2">{{ $rule->description ?? 'Sin descripción' }}</p>
                                            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
                                                <span><i class="fas fa-calendar mr-1"></i> Creada: {{ $rule->created_at->format('d/m/Y') }}</span>
                                                @if($rule->apiService)
                                                    <span><i class="fas fa-plug mr-1"></i> API: {{ $rule->apiService->name }}</span>
                                                @endif
                                                @if($rule->execution_count > 0)
                                                    <span><i class="fas fa-play mr-1"></i> Ejecuciones: {{ $rule->execution_count }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Acciones --}}
                                        <div class="flex items-center gap-2">
                                            <button type="button" 
                                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-green-100 text-green-600 hover:bg-green-200 transition"
                                                    title="Ejecutar"
                                                    onclick="alert('Funcionalidad de ejecución directa próximamente')">
                                                <i class="fas fa-play"></i>
                                            </button>
                                            <a href="{{ route('programmer.business-rules.edit', $rule) }}" 
                                               class="w-10 h-10 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('programmer.business-rules.destroy', $rule) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta regla?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="w-10 h-10 flex items-center justify-center rounded-full bg-red-100 text-red-600 hover:bg-red-200 transition"
                                                        title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Paginación --}}
                        <div class="px-6 py-4 border-t border-gray-100">
                            {{ $rules->links() }}
                        </div>
                    @else
                        <div class="px-6 py-16 text-center">
                            <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-code-branch text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No hay reglas de negocio</h3>
                            <p class="text-gray-500 mb-6">Comienza creando tu primera regla ETL</p>
                            <a href="{{ route('programmer.business-rules.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-btn-start to-btn-end text-white rounded-full font-bold text-sm uppercase tracking-widest shadow transition hover:scale-105">
                                <i class="fas fa-plus mr-2"></i> Crear Primera Regla
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
