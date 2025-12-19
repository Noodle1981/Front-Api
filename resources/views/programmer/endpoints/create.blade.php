<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-network-wired mr-2 text-aurora-cyan"></i> Gestión de Endpoints: {{ $service->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Flash Message --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- 1. List of Existing Endpoints --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-lg text-gray-800">Endpoints Definidos</h3>
                    <span class="text-xs font-semibold px-2 py-1 bg-gray-200 text-gray-600 rounded-full">{{ $endpoints->count() }} total</span>
                </div>
                
                @if($endpoints->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Método</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">URL Relativa</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                                    <th class="px-5 py-3 border-b-2 border-gray-200 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($endpoints as $endpoint)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-5 py-4 text-sm font-mono">
                                            @php
                                                $colors = [
                                                    'GET' => 'bg-green-100 text-green-800',
                                                    'POST' => 'bg-blue-100 text-blue-800',
                                                    'PUT' => 'bg-yellow-100 text-yellow-800',
                                                    'DELETE' => 'bg-red-100 text-red-800',
                                                ];
                                                $color = $colors[$endpoint->method] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs font-bold {{ $color }}">
                                                {{ $endpoint->method }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-gray-700 font-mono">{{ $endpoint->url }}</td>
                                        <td class="px-5 py-4 text-sm text-gray-500">{{ Str::limit($endpoint->description, 50) }}</td>
                                        <td class="px-5 py-4 text-right text-sm">
                                            <button onclick="toggleTestPanel({{ $endpoint->id }})" class="text-aurora-cyan hover:text-blue-600 font-bold mr-3 transition">
                                                <i class="fas fa-play mr-1"></i> Probar
                                            </button>
                                            <a href="#" class="text-gray-400 hover:text-gray-600 transition">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    {{-- Test Panel (Hidden Row) --}}
                                    <tr id="test-panel-{{ $endpoint->id }}" class="hidden bg-gray-50 shadow-inner">
                                        <td colspan="4" class="px-5 py-4">
                                            <div class="flex flex-col space-y-4">
                                                <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                                                    <h4 class="font-bold text-gray-700"><i class="fas fa-vial mr-2"></i> Probar Endpoint</h4>
                                                    <button onclick="toggleTestPanel({{ $endpoint->id }})" class="text-gray-400 hover:text-red-500"><i class="fas fa-times"></i></button>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                    {{-- Configuration --}}
                                                    <div class="md:col-span-1 space-y-3">
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 uppercase">Integración (Credenciales)</label>
                                                            <select id="integration-{{ $endpoint->id }}" class="w-full text-sm rounded border-gray-300">
                                                                @forelse($integrations as $integration)
                                                                    <option value="{{ $integration->id }}">{{ $integration->name }} ({{ $integration->client ? $integration->client->company : 'Sin Cliente' }})</option>
                                                                @empty
                                                                    <option value="">Sin integraciones activas</option>
                                                                @endforelse
                                                            </select>
                                                            @if($integrations->isEmpty())
                                                                <p class="text-xs text-red-500 mt-1">Crea una integración primero para probar.</p>
                                                            @endif
                                                        </div>
                                                        <button onclick="executeTest({{ $endpoint->id }})" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                                                            <i class="fas fa-paper-plane mr-2"></i> Enviar Petición
                                                        </button>
                                                    </div>

                                                    {{-- URL Preview --}}
                                                    <div class="md:col-span-2">
                                                         <div class="bg-gray-800 text-green-400 font-mono text-xs p-3 rounded mb-2">
                                                            <span class="text-blue-300">{{ $endpoint->method }}</span> {{ $service->base_url }}{{ $endpoint->url }}
                                                         </div>
                                                         
                                                         {{-- Response Area --}}
                                                         <div class="relative">
                                                             <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Respuesta</label>
                                                             <div id="response-{{ $endpoint->id }}" class="bg-white border border-gray-300 rounded p-3 h-40 overflow-auto font-mono text-xs text-gray-700">
                                                                 <span class="text-gray-400 italic">Esperando prueba...</span>
                                                             </div>
                                                         </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-ghost text-4xl mb-3 text-gray-300"></i>
                        <p>No hay endpoints definidos para este servicio aún.</p>
                    </div>
                @endif
            </div>

            {{-- 2. New Endpoint Form --}}
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100 p-8">
                <h3 class="font-bold text-lg text-gray-800 mb-6 border-b pb-2"><i class="fas fa-plus-circle mr-2 text-aurora-cyan"></i> Nuevo Endpoint</h3>
                
                <form action="{{ route('programmer.services.endpoints.store', $service) }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        {{-- Method --}}
                        <div>
                            <label for="method" class="block text-sm font-bold text-gray-700 mb-2">Método HTTP</label>
                            <select name="method" id="method" class="w-full rounded-lg border-gray-300 focus:border-aurora-cyan focus:ring focus:ring-aurora-cyan/20 transition shadow-sm">
                                @foreach(['GET', 'POST', 'PUT', 'DELETE'] as $verb)
                                    <option value="{{ $verb }}" {{ (old('method', $preselectedMethod) == $verb) ? 'selected' : '' }}>
                                        {{ $verb }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- URL --}}
                        <div>
                            <label for="url" class="block text-sm font-bold text-gray-700 mb-2">URL Relativa</label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-xs">
                                    {{ $service->base_url }}
                                </span>
                                <input type="text" name="url" id="url" value="{{ old('url') }}"
                                    class="flex-1 rounded-r-lg border-gray-300 focus:border-aurora-cyan focus:ring focus:ring-aurora-cyan/20 transition shadow-sm"
                                    placeholder="/v1/recurso" required>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Descripción</label>
                        <textarea name="description" id="description" rows="2"
                            class="w-full rounded-lg border-gray-300 focus:border-aurora-cyan focus:ring focus:ring-aurora-cyan/20 transition shadow-sm"
                            placeholder="Describe qué hace este endpoint...">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-aurora-cyan to-blue-500 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200">
                            Crear Endpoint <i class="fas fa-plus ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleTestPanel(id) {
            const panel = document.getElementById('test-panel-' + id);
            panel.classList.toggle('hidden');
        }

        async function executeTest(endpointId) {
            const integrationId = document.getElementById('integration-' + endpointId).value;
            const output = document.getElementById('response-' + endpointId);

            if (!integrationId) {
                alert('Selecciona una integración para probar.');
                return;
            }

            output.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ejecutando petición...';
            output.className = 'bg-gray-100 border border-gray-300 rounded p-3 h-40 overflow-auto font-mono text-xs text-gray-600';

            try {
                // We need a route for testing specific endpoint
                const response = await fetch("{{ route('programmer.endpoints.execute_test') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        endpoint_id: endpointId,
                        integration_id: integrationId
                    })
                });

                const data = await response.json();
                
                if(data.success) {
                    output.className = 'bg-gray-900 border border-gray-800 rounded p-3 h-40 overflow-auto font-mono text-xs text-green-400';
                    output.textContent = JSON.stringify(data.data, null, 2);
                } else {
                    output.className = 'bg-red-50 border border-red-200 rounded p-3 h-40 overflow-auto font-mono text-xs text-red-600';
                    output.textContent = 'Error: ' + data.message + '\n\n' + JSON.stringify(data.details, null, 2);
                }

            } catch (error) {
                output.textContent = 'Error de conexión: ' + error.message;
            }
        }
    </script>
</x-app-layout>
