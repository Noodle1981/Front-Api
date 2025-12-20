<div>
    {{-- Filtros --}}
    <div class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg border border-white/30 p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">
            {{-- Buscador en tiempo real --}}
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Buscar por nombre, CUIT, email..."
                       class="w-full pl-10 pr-4 py-2 rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
            </div>
            
            {{-- Filtro por estado --}}
            <div class="w-full md:w-48">
                <select wire:model.live="status" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                    <option value="">Todos los estados</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </select>
            </div>
            
            {{-- Contador de resultados --}}
            <div class="flex gap-3 text-sm">
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700">
                    <i class="fas fa-users mr-1"></i> {{ $totalClients }} total
                </span>
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700">
                    <i class="fas fa-check-circle mr-1"></i> {{ $activeClients }} activos
                </span>
            </div>
        </div>
    </div>

    {{-- Loading spinner --}}
    <div wire:loading class="flex justify-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-aurora-cyan"></div>
    </div>

    {{-- Tabla de resultados --}}
    <div wire:loading.remove class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg border border-white/30 overflow-hidden">
        @if($clients->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($clients as $client)
                    <div class="px-6 py-4 hover:bg-gray-50/50 transition flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="font-semibold text-gray-800">{{ $client->company }}</h3>
                                @if($client->fantasy_name)
                                    <span class="text-gray-500 text-sm">({{ $client->fantasy_name }})</span>
                                @endif
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $client->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                            <div class="flex gap-4 mt-1 text-sm text-gray-500">
                                @if($client->cuit)
                                    <span><i class="fas fa-id-card mr-1"></i> {{ $client->cuit }}</span>
                                @endif
                                @if($client->email)
                                    <span><i class="fas fa-envelope mr-1"></i> {{ $client->email }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('programmer.clients.show', $client) }}" 
                               class="w-9 h-9 flex items-center justify-center rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                               title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('programmer.clients.edit', $client) }}" 
                               class="w-9 h-9 flex items-center justify-center rounded-full bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition"
                               title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Paginación --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $clients->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No se encontraron clientes</p>
            </div>
        @endif
    </div>
</div>
