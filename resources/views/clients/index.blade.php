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
                                <td class="p-4 text-black">
                                    @if($client->active)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-right text-base font-medium">
                                    <div class="flex items-center justify-end space-x-4">
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="text-gray-600 hover:text-pink-500 transition" title="Ver Detalles">
                                            <i class="fas fa-search"></i>
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
                                            <form action="{{ route('clients.deactivate', $client) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('¿Estás seguro? El cliente será marcado como inactivo y no se mostrará en el listado principal.');">
                                                @csrf
                                                <button type="submit" class="text-gray-600 hover:text-red-500 transition"
                                                    title="Desactivar Cliente">
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