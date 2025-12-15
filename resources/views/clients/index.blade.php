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
                class="px-4 py-2 rounded-lg font-bold text-white bg-primary-dark hover:bg-primary transition @if($filter === 'activos') ring-2 ring-primary-light @endif">Clientes
                Activos</a>
            <a href="{{ route('clients.index', ['filter' => 'inactivos']) }}"
                class="px-4 py-2 rounded-lg font-bold text-white bg-orange-xamanen hover:bg-orange-600 transition @if($filter === 'inactivos') ring-2 ring-orange-xamanen @endif">Clientes
                Inactivos</a>
        </div>
        <x-card class="!p-0 bg-white border border-primary-light shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-base">
                    <thead class="border-b border-primary-light bg-surface">
                        <tr>
                            <th class="p-4 text-left font-bold text-primary-dark uppercase tracking-wider">Nombre</th>
                            <th class="p-4 text-left font-bold text-primary-dark uppercase tracking-wider">Compañía</th>
                            <th class="p-4 text-left font-bold text-primary-dark uppercase tracking-wider">Email</th>
                            <th class="p-4 text-left font-bold text-primary-dark uppercase tracking-wider">Teléfono</th>
                            <th class="p-4 text-left font-bold text-primary-dark uppercase tracking-wider">Estado
                                Cliente</th>
                            <th class="relative p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-light">
                        @forelse ($clients as $client)
                            <tr class="hover:bg-primary-light/20 transition-colors duration-200">
                                <td class="p-4 font-bold text-black">{{ $client->name }}</td>
                                <td class="p-4 text-black">{{ $client->company }}</td>
                                <td class="p-4 text-black">{{ $client->email }}</td>
                                <td class="p-4 text-black">{{ $client->phone ?? 'N/A' }}</td>
                                <td class="p-4 text-black">{{ ucfirst($client->client_status) }}</td>
                                <td class="p-4 text-right text-base font-medium">
                                    <div class="flex items-center justify-end space-x-4">
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="text-primary-dark hover:text-primary transition" title="Ver Detalles">
                                            <i class="fas fa-search"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}"
                                            class="text-primary-dark hover:text-primary transition" title="Editar Cliente">
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
                                                <button type="submit"
                                                    class="text-primary-dark hover:text-orange-xamanen transition"
                                                    title="Desactivar Cliente">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-primary-dark py-16">
                                    <i class="fas fa-users-slash text-4xl mb-3"></i>
                                    <p>No se encontraron clientes.</p>
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