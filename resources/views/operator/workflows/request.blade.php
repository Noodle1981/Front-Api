<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-paper-plane mr-2 text-brand-dark"></i>Solicitar Workflow
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    <!-- Encabezado -->
                    <div class="mb-6 pb-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">Nueva Solicitud de Workflow</h3>
                        <p class="text-gray-500 text-sm mt-1">
                            Completa el formulario para solicitar la creación de un nuevo workflow al equipo de programación.
                        </p>
                    </div>

                    <!-- Formulario -->
                    <form action="{{ route('operator.workflows.request.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Cliente -->
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building mr-1"></i> Cliente
                            </label>
                            <select name="client_id" id="client_id" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                                <option value="">-- Selecciona un cliente --</option>
                                @foreach(auth()->user()->clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->company }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sede/Sucursal -->
                        <div>
                            <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i> Sede / Sucursal
                            </label>
                            <select name="branch_id" id="branch_id" required
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                                <option value="">-- Selecciona una sede --</option>
                                @foreach(auth()->user()->clients as $client)
                                    @foreach($client->children as $branch)
                                        <option value="{{ $branch->id }}" data-parent="{{ $client->id }}">
                                            {{ $client->company }} - {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                Selecciona primero el cliente para filtrar las sedes disponibles
                            </p>
                        </div>


                        <!-- Tipo de Workflow -->
                        <div>
                            <label for="workflow_type" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-project-diagram mr-1"></i> Tipo de Workflow
                            </label>
                            
                            @php
                                $availableTypes = [
                                    'conciliacion' => 'Conciliación',
                                    // Futuros tipos se agregarán aquí
                                    // 'facturacion' => 'Facturación',
                                    // 'reportes' => 'Generación de Reportes',
                                ];
                                $typeCount = count($availableTypes);
                            @endphp
                            
                            @if($typeCount === 1)
                                {{-- Solo un tipo disponible: auto-seleccionar y mostrar como readonly --}}
                                @php
                                    $singleType = array_key_first($availableTypes);
                                    $singleTypeName = $availableTypes[$singleType];
                                @endphp
                                <input type="hidden" name="workflow_type" value="{{ $singleType }}">
                                <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    {{ $singleTypeName }}
                                    <span class="text-xs text-gray-500 ml-2">(único disponible)</span>
                                </div>
                            @else
                                {{-- Múltiples tipos: mostrar selector --}}
                                <select name="workflow_type" id="workflow_type" required
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                                    <option value="">-- Selecciona un tipo --</option>
                                    @foreach($availableTypes as $code => $name)
                                        <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>


                        <!-- Título de la solicitud -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-heading mr-1"></i> Título de la Solicitud
                            </label>
                            <input type="text" name="title" id="title" required
                                placeholder="Ej: Conciliación mensual de ventas"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-1"></i> Descripción Detallada
                            </label>
                            <textarea name="description" id="description" rows="4" required
                                placeholder="Describe qué necesitas, qué archivos se usarán, qué resultado esperas..."
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent"></textarea>
                        </div>

                        <!-- Prioridad -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-flag mr-1"></i> Prioridad
                            </label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="priority" value="low" class="form-radio text-green-500">
                                    <span class="ml-2 text-sm text-gray-700">
                                        <i class="fas fa-arrow-down text-green-500"></i> Baja
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="priority" value="medium" checked class="form-radio text-yellow-500">
                                    <span class="ml-2 text-sm text-gray-700">
                                        <i class="fas fa-minus text-yellow-500"></i> Media
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="priority" value="high" class="form-radio text-red-500">
                                    <span class="ml-2 text-sm text-gray-700">
                                        <i class="fas fa-arrow-up text-red-500"></i> Alta
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Fecha esperada -->
                        <div>
                            <label for="expected_date" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1"></i> Fecha Esperada (Opcional)
                            </label>
                            <input type="date" name="expected_date" id="expected_date"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-brand-accent focus:border-brand-accent">
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('dashboard') }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Cancelar
                            </a>
                            <button type="submit" 
                                class="px-6 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-800 transition">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info adicional -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">¿Cómo funciona?</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            Tu solicitud será enviada al equipo de programación. Recibirás una notificación 
                            cuando el workflow esté listo para ser utilizado.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clientSelect = document.getElementById('client_id');
            const branchSelect = document.getElementById('branch_id');
            const allBranches = Array.from(branchSelect.querySelectorAll('option[data-parent]'));

            // Filter branches when client changes
            clientSelect.addEventListener('change', function() {
                const selectedClientId = this.value;
                
                // Reset branch select
                branchSelect.value = '';
                
                // Show/hide branches based on selected client
                allBranches.forEach(option => {
                    if (!selectedClientId || option.dataset.parent === selectedClientId) {
                        option.style.display = '';
                    } else {
                        option.style.display = 'none';
                    }
                });
            });

            // Initialize on page load
            clientSelect.dispatchEvent(new Event('change'));
        });
    </script>
    @endpush
</x-app-layout>
