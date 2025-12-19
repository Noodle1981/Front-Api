<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-cogs mr-2 text-aurora-pink"></i> Configurar {{ $provider->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl border border-gray-100">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    
                    {{-- Left Side: Provider Info --}}
                    <div class="md:col-span-1 bg-gray-50 p-6 border-r border-gray-100 flex flex-col items-center text-center">
                        @if($provider->logo_url)
                            <img src="{{ asset($provider->logo_url) }}" alt="{{ $provider->name }}" class="w-24 h-24 object-contain mb-4">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center text-4xl text-gray-400 mb-4">
                                <i class="fas fa-cube"></i>
                            </div>
                        @endif
                        <h3 class="text-lg font-bold text-gray-800">{{ $provider->name }}</h3>
                        <p class="text-sm text-gray-500 mt-2">{{ $provider->base_url }}</p>
                        
                        <div class="mt-6 w-full text-left">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Endpoints</h4>
                            <ul class="text-xs text-gray-600 space-y-1">
                                @forelse($provider->endpoints as $endpoint)
                                    <li class="flex items-center">
                                        <span class="w-12 font-mono font-bold {{ $endpoint->method == 'GET' ? 'text-blue-500' : 'text-green-500' }}">{{ $endpoint->method }}</span>
                                        <span class="truncate">{{ Str::limit($endpoint->url, 20) }}</span>
                                    </li>
                                @empty
                                    <li class="italic text-gray-400">Sin endpoints definidos</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    {{-- Right Side: Configuration Form --}}
                    <div class="md:col-span-2 p-8">
                        <form action="{{ route('programmer.integrations.store', $provider) }}" method="POST">
                            @csrf
                            
                            <h3 class="text-xl font-bold text-brand-dark mb-6 border-b pb-2">Datos de Conexión</h3>
                            
                            {{-- Name (Optional) --}}
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nombre de Referencia (Opcional)</label>
                                <input type="text" name="name" id="name" 
                                    class="w-full rounded-lg border-gray-300 focus:border-aurora-cyan focus:ring focus:ring-aurora-cyan/20 transition shadow-sm"
                                    placeholder="Ej: Mercado Pago - Sucursal Centro">
                            </div>

                            {{-- Client Selection --}}
                            <div class="mb-6">
                                <label for="client_id" class="block text-sm font-bold text-gray-700 mb-2">Cliente a Vincular (Opcional)</label>
                                <p class="text-xs text-gray-500 mb-2">Puedes dejarlo vacío y asociarlo más tarde.</p>
                                <select name="client_id" id="client_id"
                                    class="w-full rounded-lg border-gray-300 focus:border-aurora-cyan focus:ring focus:ring-aurora-cyan/20 transition shadow-sm">
                                    <option value="">-- No vincular por ahora --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">
                                            {{ $client->company }} @if($client->fantasy_name) ({{ $client->fantasy_name }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Dynamic Credentials Fields --}}
                            @if($provider->required_fields)
                                <div class="bg-blue-50 p-4 rounded-lg mb-6 border border-blue-100">
                                    <h4 class="text-sm font-bold text-blue-800 mb-3 flex items-center">
                                        <i class="fas fa-key mr-2"></i> Credenciales Requeridas
                                    </h4>
                                    <div class="space-y-4">
                                        @foreach($provider->required_fields as $field)
                                            <div>
                                                <label for="field_{{ $loop->index }}" class="block text-xs font-bold text-gray-600 uppercase mb-1">
                                                    {{ str_replace('_', ' ', $field) }}
                                                </label>
                                                <input type="text" name="credentials[{{ $field }}]" id="field_{{ $loop->index }}" required
                                                    class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
                                                    placeholder="Ingrese {{ str_replace('_', ' ', $field) }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 p-4 rounded text-sm text-yellow-700 mb-6">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Este proveedor no requiere credenciales adicionales.
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                {{-- Test Status --}}
                                <div id="test-result" class="text-sm font-bold"></div>

                                <div class="flex space-x-3">
                                    <button type="button" id="btn-test-connection"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition-colors">
                                        <i class="fas fa-plug mr-2"></i> Probar Conexión
                                    </button>
                                    
                                    <button type="submit" 
                                        class="px-6 py-2 bg-gradient-to-r from-aurora-cyan to-blue-500 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200">
                                        Guardar Integración <i class="fas fa-save ml-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('btn-test-connection').addEventListener('click', async function() {
            const btn = this;
            const resultDiv = document.getElementById('test-result');
            const form = btn.closest('form');
            const formData = new FormData(form);
            
            // Add provider ID manually if not in form inputs (it is in route param, but for test endpoint we need it)
            // Or pass it via data attribute. 
            // Better: Add hidden input or pass via JS.
            formData.append('provider_id', '{{ $provider->id }}');

            // UI Loading state
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Probando...';
            resultDiv.className = 'text-sm font-bold text-gray-500';
            resultDiv.innerHTML = 'Verificando credenciales...';

            try {
                const response = await fetch("{{ route('programmer.integrations.test') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    resultDiv.className = 'text-sm font-bold text-green-600';
                    resultDiv.innerHTML = '<i class="fas fa-check-circle mr-1"></i> ' + data.message;
                } else {
                    throw new Error(data.message || 'Error en la conexión');
                }
            } catch (error) {
                resultDiv.className = 'text-sm font-bold text-red-600';
                resultDiv.innerHTML = '<i class="fas fa-times-circle mr-1"></i> ' + error.message;
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plug mr-2"></i> Probar Conexión';
            }
        });
    </script>
</x-app-layout>
