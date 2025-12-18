<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>{{ __('Clientes') }}</span>
            <a href="{{ route('clients.create') }}">
                <x-primary-button>
                    <i class="fas fa-user-plus mr-2"></i>
                    Crear Nuevo Cliente
                </x-primary-button>
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-4">
            <x-auth-session-status :status="session('success')" />
        </div>
    @endif

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- User Context Selector (Only for Analysts/Admins) --}}
        @if($contextUsers->isNotEmpty())
            <div class="mb-6 bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-filter text-brand-dark mr-3 text-xl"></i>
                        <div>
                            <h3 class="text-sm font-bold text-gray-800">Vista de Contexto</h3>
                            <p class="text-xs text-gray-500">Filtrar clientes por contador específico</p>
                        </div>
                    </div>
                    
                    <form method="GET" action="{{ route('clients.index') }}" class="flex items-center gap-3">
                        <select name="user_filter" onchange="this.form.submit()" 
                            class="text-sm border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent bg-white/90">
                            <option value="">📊 General (Todos los contadores)</option>
                            @foreach($contextUsers as $contextUser)
                                <option value="{{ $contextUser->id }}" {{ $selectedUser && $selectedUser->id == $contextUser->id ? 'selected' : '' }}>
                                    👤 {{ $contextUser->name }}
                                </option>
                            @endforeach
                        </select>
                        
                        @if($selectedUser)
                            <a href="{{ route('clients.index') }}" 
                               class="px-3 py-2 text-xs bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-times mr-1"></i> Limpiar
                            </a>
                        @endif
                        
                        <input type="hidden" name="filter" value="{{ $filter }}">
                    </form>
                </div>
                
                @if($selectedUser)
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <div class="flex items-center text-sm">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold mr-2">
                                <i class="fas fa-eye mr-1"></i> Vista Filtrada
                            </span>
                            <span class="text-gray-600">Mostrando clientes de: <strong class="text-gray-900">{{ $selectedUser->name }}</strong></span>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="mb-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-4 border border-white/20">
                <div class="text-xs text-gray-500 uppercase mb-1">Total</div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['total_clients'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 shadow-lg rounded-lg p-4 border border-green-200">
                <div class="text-xs text-green-700 uppercase mb-1">Activos</div>
                <div class="text-2xl font-bold text-green-700">{{ $stats['active_clients'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 shadow-lg rounded-lg p-4 border border-red-200">
                <div class="text-xs text-red-700 uppercase mb-1">Inactivos</div>
                <div class="text-2xl font-bold text-red-700">{{ $stats['inactive_clients'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 shadow-lg rounded-lg p-4 border border-blue-200">
                <div class="text-xs text-blue-700 uppercase mb-1">Sedes</div>
                <div class="text-2xl font-bold text-blue-700">{{ $stats['headquarters'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 shadow-lg rounded-lg p-4 border border-purple-200">
                <div class="text-xs text-purple-700 uppercase mb-1">Sucursales</div>
                <div class="text-2xl font-bold text-purple-700">{{ $stats['branches'] }}</div>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 shadow-lg rounded-lg p-4 border border-orange-200">
                <div class="text-xs text-orange-700 uppercase mb-1">Con APIs</div>
                <div class="text-2xl font-bold text-orange-700">{{ $stats['with_apis'] }}</div>
            </div>
        </div>

        <div class="mb-6 flex gap-4">
            <a href="{{ route('clients.index', ['filter' => 'activos']) }}"
                class="px-4 py-2 rounded-lg font-bold text-white bg-brand-dark hover:bg-gray-800 transition @if($filter === 'activos') ring-2 ring-brand-accent @endif">Clientes
                Activos</a>
            <a href="{{ route('clients.index', ['filter' => 'inactivos']) }}"
                class="px-4 py-2 rounded-lg font-bold text-white bg-gray-500 hover:bg-gray-600 transition @if($filter === 'inactivos') ring-2 ring-brand-accent @endif">Clientes
                Inactivos</a>
        </div>
        <x-card class="!p-0 bg-white border border-gray-200 shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-base">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="p-4 text-left font-bold text-brand-dark uppercase tracking-wider">Razón Social y
                                CUIT</th>
                            <th class="p-4 text-left font-bold text-brand-dark uppercase tracking-wider">Condición
                                Fiscal</th>
                            <th class="p-4 text-left font-bold text-brand-dark uppercase tracking-wider">Contacto</th>
                            @can('reassign clients')
                                <th class="p-4 text-left font-bold text-brand-dark uppercase tracking-wider">Responsable</th>
                            @endcan
                            <th class="p-4 text-left font-bold text-brand-dark uppercase tracking-wider">Estado</th>
                            <th class="relative p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-light">
                        @forelse ($clients as $client)
                            <tr class="hover:bg-primary-light/20 transition-colors duration-200">
                                <td class="p-4">
                                    <div class="font-bold text-black">{{ $client->company }}</div>
                                    <div class="text-sm text-gray-500">{{ $client->cuit }}</div>
                                    @if($client->fantasy_name)
                                        <div class="text-xs text-gray-400 mt-1">{{ $client->fantasy_name }}</div>
                                    @endif
                                </td>
                                <td class="p-4 text-black">{{ $client->tax_condition ?? 'N/A' }}</td>
                                <td class="p-4">
                                    <div class="text-black">{{ $client->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $client->phone ?? 'N/A' }}</div>
                                </td>
                                @can('reassign clients')
                                    <td class="p-4">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs mr-2">
                                                {{ substr($client->user->name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">{{ $client->user->name ?? 'Sin asignar' }}</div>
                                        </div>
                                    </td>
                                @endcan
                                <td class="p-4 text-black">
                                    @if($client->active)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <div>
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactivo
                                            </span>
                                            @if($client->deactivation_reason)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @if($client->deactivation_reason === 'Falta de Pago')
                                                        <i class="fas fa-file-invoice-dollar text-red-600 mr-1"></i>
                                                    @else
                                                        <i class="fas fa-info-circle text-gray-400 mr-1"></i>
                                                    @endif
                                                    {{ $client->deactivation_reason }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4 text-right text-base font-medium">
                                    <div class="flex items-center justify-end space-x-4">
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="text-gray-600 hover:text-pink-500 transition" title="Gestionar Integraciones / Ver Detalles">
                                            <i class="fas fa-plug"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}"
                                            class="text-gray-600 hover:text-pink-500 transition" title="Editar Cliente">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @if($filter === 'inactivos')
                                            <form action="{{ route('clients.activate', $client) }}" method="POST" class="inline"
                                                onsubmit="return confirm('¿Seguro que quieres reactivar este cliente?');">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800 transition"
                                                    title="Reactivar Cliente">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            {{-- Botón Suspensión por Falta de Pago --}}
                                            <form action="{{ route('clients.deactivate', $client) }}" method="POST"
                                                class="inline mr-1"
                                                onsubmit="return confirm('¿Suspender cliente por FALTA DE PAGO?');">
                                                @csrf
                                                <input type="hidden" name="reason" value="Falta de Pago">
                                                <button type="submit" class="text-red-600 hover:text-red-800 transition"
                                                    title="Suspender por Falta de Pago">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('clients.deactivate', $client) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('¿Estás seguro? El cliente será marcado como inactivo y no se mostrará en el listado principal.');">
                                                @csrf
                                                <button type="submit" class="text-gray-400 hover:text-gray-600 transition"
                                                    title="Desactivar (Otro motivo)">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @can('delete clients')
                                            <form action="{{ route('clients.destroy', $client) }}" method="POST"
                                                class="inline ml-2"
                                                onsubmit="return confirm('¿Eliminar cliente permanentemente? (Soft Delete)');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm text-red-400 hover:text-red-600 transition"
                                                    title="Eliminar (Solo Admin)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endcan

                                        @can('reassign clients')
                                            <a href="{{ route('analyst.clients.transfer', $client) }}"
                                                class="text-sm text-blue-500 hover:text-blue-700 transition ml-2"
                                                title="Reasignar (Transferir)">
                                                <i class="fas fa-exchange-alt"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-16">
                                    <i class="fas fa-users-slash text-4xl mb-3 text-brand-dark"></i>
                                    <p class="text-lg">No se encontraron clientes.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($clients->hasPages())
                <div class="p-4 border-t border-primary-light">
                    {{ $clients->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>