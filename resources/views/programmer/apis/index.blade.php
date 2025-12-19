<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de APIs') }}
            </h2>
            <a href="{{ route('programmer.apis.create') }}" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-btn-start to-btn-end hover:to-btn-start text-white border border-transparent rounded-full font-bold text-xs uppercase tracking-widest shadow-[0_4px_14px_0_rgba(247,131,143,0.39)] transition-all ease-in-out duration-300 transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i> Nueva API
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            API / Integración
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Proveedor
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Cliente Vinculado
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apis as $api)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-no-wrap font-bold">
                                            {{ $api->name ?? 'Sin Nombre' }}
                                        </p>
                                        <p class="text-gray-600 whitespace-no-wrap text-xs">ID: {{ $api->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center">
                                    @if($api->apiService->logo_url)
                                        <img src="{{ asset($api->apiService->logo_url) }}" alt="" class="w-6 h-6 mr-2 object-contain">
                                    @endif
                                    <span class="text-gray-900 whitespace-no-wrap">{{ $api->apiService->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                @if($api->client)
                                    <span class="relative inline-block px-3 py-1 font-semibold text-blue-900 leading-tight">
                                        <span aria-hidden class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"></span>
                                        <span class="relative">{{ $api->client->company }}</span>
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Sin Asignar</span>
                                @endif
                            </td>
                             <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $api->is_active ? 'text-green-900' : 'text-red-900' }}">
                                    <span aria-hidden class="absolute inset-0 {{ $api->is_active ? 'bg-green-200' : 'bg-red-200' }} opacity-50 rounded-full"></span>
                                    <span class="relative">{{ $api->is_active ? 'Activo' : 'Inactivo' }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    {{-- Endpoint Shortcuts --}}
                                    <a href="{{ route('programmer.services.endpoints.create', ['service' => $api->api_service_id, 'method' => 'GET']) }}" 
                                       class="px-2 py-1 text-xs font-bold text-white bg-green-500 rounded hover:bg-green-600 transition shadow-sm" title="Agregar Endpoint GET">
                                        + GET
                                    </a>
                                    <a href="{{ route('programmer.services.endpoints.create', ['service' => $api->api_service_id, 'method' => 'POST']) }}" 
                                       class="px-2 py-1 text-xs font-bold text-white bg-blue-500 rounded hover:bg-blue-600 transition shadow-sm mr-2" title="Agregar Endpoint POST">
                                        + POST
                                    </a>

                                    {{-- Edit Link --}}
                                    <a href="{{ route('programmer.apis.edit', $api->id) }}" class="text-gray-400 hover:text-aurora-cyan transition transform hover:scale-110" title="Editar Configuración">
                                        <i class="fas fa-cog text-lg"></i>
                                    </a>
                                    
                                    <form action="{{ route('programmer.apis.destroy', $api->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta integración?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500 transition transform hover:scale-110" title="Eliminar Integración">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
                        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
                            {{ $apis->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
