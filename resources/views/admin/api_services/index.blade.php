<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>{{ __('Catálogo de Servicios API') }}</span>
            <a href="{{ route('admin.api-services.create') }}">
                <x-primary-button>
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Servicio
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
        <x-card class="!p-0 bg-white border border-gray-200 shadow-lg">
            <div class="overflow-x-auto">
                <table class="w-full text-base">
                    <thead class="border-b border-gray-200 bg-gray-50">
                        <tr>
                            <th class="p-4 text-left font-bold text-gray-700 uppercase tracking-wider">Nombre</th>
                            <th class="p-4 text-left font-bold text-gray-700 uppercase tracking-wider">Slug / ID</th>
                            <th class="p-4 text-left font-bold text-gray-700 uppercase tracking-wider">Campos Requeridos
                            </th>
                            <th class="p-4 text-right font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($services as $service)
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="p-4">
                                    <div class="font-bold text-gray-900">{{ $service->name }}</div>
                                    @if($service->base_url)
                                        <div class="text-xs text-blue-500 font-mono mt-1">{{ $service->base_url }}</div>
                                    @endif
                                </td>
                                <td class="p-4 font-mono text-sm text-gray-600">
                                    {{ $service->slug }}
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($service->required_fields as $field)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                {{ $field }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('admin.api-services.edit', $service) }}"
                                            class="text-gray-500 hover:text-aurora-pink transition" title="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form action="{{ route('admin.api-services.destroy', $service) }}" method="POST"
                                            onsubmit="return confirm('¿Estás seguro? Se borrará este servicio del catálogo.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-500 hover:text-red-600 transition"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 py-12">
                                    <i class="fas fa-cubes text-4xl mb-3 opacity-30"></i>
                                    <p>No hay servicios definidos aún.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>