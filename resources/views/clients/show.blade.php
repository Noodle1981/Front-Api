<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>Detalles del Cliente: {{ $client->fantasy_name ?? $client->company }}</span>
            <a href="{{ route('clients.edit', $client) }}">
                <x-secondary-button>
                    <i class="fas fa-pen mr-2"></i>
                    Editar Cliente
                </x-secondary-button>
            </a>
            <a href="{{ route('clients.index') }}" class="text-light-text-muted hover:text-light-text transition"
                title="Volver a la lista de clientes">
                <i class="fas fa-arrow-left text-2xl"></i>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Columna Izquierda: Detalles y Contactos -->
            <div class="lg:col-span-1 space-y-8">

                <!-- ALERTA DE JERARQUÍA -->
                @if($client->parent)
                    <div
                        class="bg-blue-900/30 border border-blue-500/30 p-4 rounded-lg text-blue-200 relative overflow-hidden">
                        <div class="relative z-10">
                            <i class="fas fa-level-up-alt mr-2 text-xl"></i>
                            Este cliente es un <strong>ANEXO</strong> de:
                            <a href="{{ route('clients.show', $client->parent) }}"
                                class="underline font-bold text-white hover:text-aurora-cyan block mt-1">
                                {{ $client->parent->company }}
                            </a>
                            @if($client->branch_name)
                                <div class="mt-2 text-sm text-blue-300">
                                    <i class="fas fa-store mr-1"></i> Sucursal: <strong>{{ $client->branch_name }}</strong>
                                </div>
                            @endif
                        </div>
                        <div class="absolute -right-4 -bottom-4 text-blue-500/10 text-9xl z-0">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                @endif

                @if($client->children->isNotEmpty())
                    <div class="bg-purple-900/30 border border-purple-500/30 p-4 rounded-lg text-purple-200">
                        <div class="font-bold flex items-center mb-3">
                            <i class="fas fa-network-wired mr-2"></i> Sucursales / Anexos ({{ $client->children->count() }})
                        </div>
                        <ul class="space-y-2 ml-2">
                            @foreach($client->children as $child)
                                <li class="flex items-center">
                                    <i class="fas fa-chevron-right text-xs mr-2 opacity-50"></i>
                                    <a href="{{ route('clients.show', $child) }}"
                                        class="hover:text-white hover:underline transition">
                                        {{ $child->branch_name ?? $child->fantasy_name ?? $child->company }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tarjeta de Información Fiscal -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-building text-xl text-aurora-cyan"></i>
                            <h3 class="font-headings text-xl text-gray-900">Información Fiscal</h3>
                        </div>
                    </x-slot>
                    <div class="space-y-3 text-gray-600">
                        <p><strong>Razón Social:</strong> <span
                                class="text-gray-900">{{ $client->company ?? 'N/A' }}</span></p>
                        <p><strong>CUIT:</strong> <span class="text-gray-900">{{ $client->cuit ?? 'N/A' }}</span></p>
                        @if($client->fantasy_name)
                            <p><strong>Nombre Fantasía:</strong> <span
                                    class="text-gray-900">{{ $client->fantasy_name }}</span></p>
                        @endif
                        <p><strong>Condición Fiscal:</strong> <span
                                class="text-gray-900">{{ $client->tax_condition ?? 'N/A' }}</span></p>

                        <!-- Nuevos Campos Stage 6 -->
                        <p><strong>Rubro:</strong> <span class="text-gray-900">{{ $client->industry ?? '-' }}</span>
                        </p>
                        <p><strong>Empleados:</strong> <span
                                class="text-gray-900">{{ $client->employees_count ?? '-' }}</span></p>

                        <div class="border-t border-gray-200 pt-2 mt-2">
                            <p class="font-bold text-aurora-cyan mb-1">Dirección:</p>
                            <span class="text-gray-900 block">{{ $client->address ?? '-' }}</span>
                            <span class="text-gray-900 block">
                                {{ $client->city }}{{ $client->city && $client->state ? ',' : '' }} {{ $client->state }}
                            </span>
                            @if($client->zip_code)
                                <span class="text-gray-900 block">CP: {{ $client->zip_code }}</span>
                            @endif
                        </div>
                    </div>
                </x-card>

                <!-- Tarjeta de Información de Contacto -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-info-circle text-xl text-aurora-cyan"></i>
                            <h3 class="font-headings text-xl text-gray-900">Información de Contacto</h3>
                        </div>
                    </x-slot>
                    <div class="space-y-3 text-gray-600">
                        <p><strong>Email:</strong> <span class="text-gray-900">{{ $client->email ?? 'N/A' }}</span>
                        </p>
                        <div class="flex justify-between items-center">
                            <p><strong>Teléfono:</strong> <span
                                    class="text-gray-900">{{ $client->phone ?? 'N/A' }}</span></p>

                            @if ($client->phone)
                                <a href="https://wa.me/{{ $client->phone }}" target="_blank"
                                    class="text-green-400 hover:text-green-300 transition text-2xl"
                                    title="Contactar por WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            @endif
                        </div>
                        @if($client->internal_notes)
                            <div class="pt-3 border-t border-gray-200">
                                <p><strong>Notas Internas:</strong></p>
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $client->internal_notes }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>

            <!-- Columna Derecha (Principal): Integraciones y Credenciales -->
            <div class="lg:col-span-2 space-y-8">

                {{-- LISTADO DE CREDENCIALES --}}
                <x-card>
                    <x-slot name="header">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-plug text-xl text-aurora-cyan"></i>
                            <h3 class="font-headings text-xl text-light-text">Integraciones Activas</h3>
                        </div>
                    </x-slot>

                    @if($client->credentials->isEmpty())
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-network-wired text-4xl mb-3 opacity-50"></i>
                            <p>No hay integraciones configuradas para este cliente.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($client->credentials as $credential)
                                <div x-data="{
                                    isEditing: false,
                                    autoMode: '{{ $credential->execution_frequency ? 'custom' : 'manual' }}',
                                    init() {
                                        // Parsear CRON si existe
                                        let freq = '{{ $credential->execution_frequency }}'; 
                                        if(freq) {
                                            // Format: min hour * * days
                                            let parts = freq.split(' ');
                                            if(parts.length >= 5) {
                                                let min = parts[0].padStart(2, '0');
                                                let hour = parts[1].padStart(2, '0');
                                                this.$nextTick(() => {
                                                    document.getElementById('sched_time_{{ $credential->id }}').value = `${hour}:${min}`;
                                                    
                                                    // Chequear dias
                                                    let days = parts[4].split(',');
                                                    days.forEach(d => {
                                                        let chk = document.getElementById(`day_{{ $credential->id }}_${d}`);
                                                        if(chk) chk.checked = true;
                                                    });
                                                });
                                            }
                                        }
                                    }
                                }" class="p-4 rounded-lg border border-white/10" style="background-color: #0C263B;">
                                    
                                    {{-- MODO VISTA --}}
                                    <div x-show="!isEditing" class="flex justify-between items-center">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-full bg-brand-dark/50 flex items-center justify-center text-aurora-cyan border border-aurora-cyan/30">
                                                <i class="fas fa-server"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-white">{{ $credential->apiService->name }}</h4>
                                                <div class="flex gap-2">
                                                    <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full">Activo</span>
                                                    @if($credential->execution_frequency)
                                                        <span class="text-xs text-blue-400 bg-blue-400/10 px-2 py-0.5 rounded-full">Automático</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button @click="isEditing = true" type="button" 
                                                class="text-aurora-cyan hover:text-cyan-300 text-sm flex items-center gap-1 transition-colors px-3 py-1 rounded hover:bg-aurora-cyan/10">
                                                <i class="fas fa-pencil-alt"></i> Editar
                                            </button>
                                            
                                            <form action="{{ route('credentials.destroy', $credential) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar esta integración?');">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    class="text-red-400 hover:text-red-300 text-sm flex items-center gap-1 transition-colors px-3 py-1 rounded hover:bg-red-400/10">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- MODO EDICIÓN --}}
                                    <form x-show="isEditing" action="{{ route('credentials.update', $credential) }}" method="POST" class="space-y-4 mt-2 border-t border-white/10 pt-4">
                                        @csrf
                                        @method('PUT')
                                        
                                        <h5 class="text-white font-bold mb-2">Editar Credenciales - {{ $credential->apiService->name }}</h5>

                                        @foreach($credential->apiService->required_fields as $field)
                                            <div>
                                                <label class="block font-medium text-xs text-gray-400 mb-1 capitalize">{{ str_replace('_', ' ', $field) }}</label>
                                                {{-- Nota: No podemos mostrar el valor desencriptado en el value por seguridad, solo si queremos permitir editarlo.
                                                     Para este caso, asumimos que el usuario re-ingresa si quiere cambiar, o mantenemos el input vacío con placeholder.
                                                     Pero Laravel Model Casting podría devolver el array desencriptado si se accede. --}}
                                                <input type="text" name="credentials[{{ $field }}]" value="{{ $credential->credentials[$field] ?? '' }}"
                                                    class="w-full bg-gray-900 border border-gray-700 rounded text-white p-2 text-sm focus:border-aurora-cyan focus:ring-1 focus:ring-aurora-cyan outline-none">
                                            </div>
                                        @endforeach

                                        {{-- Automatización (Copia de la lógica de creación) --}}
                                        <div class="border-t border-white/10 pt-4 mt-2">
                                            <div class="flex items-center space-x-4 mb-4">
                                                <label class="inline-flex items-center">
                                                    <input type="radio" x-model="autoMode" name="automation_type" value="manual"
                                                        class="form-radio text-aurora-cyan focus:ring-aurora-cyan bg-gray-900 border-gray-700">
                                                    <span class="ml-2 text-gray-300">Manual</span>
                                                </label>
                                                <label class="inline-flex items-center">
                                                    <input type="radio" x-model="autoMode" name="automation_type" value="custom"
                                                        class="form-radio text-aurora-cyan focus:ring-aurora-cyan bg-gray-900 border-gray-700">
                                                    <span class="ml-2 text-gray-300">Automático</span>
                                                </label>
                                            </div>

                                            <div x-show="autoMode === 'custom'" class="bg-gray-900 p-3 rounded border border-gray-700 space-y-3">
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-400 mb-2">Días de Ejecución</label>
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach(['Lun' => 1, 'Mar' => 2, 'Mié' => 3, 'Jue' => 4, 'Vie' => 5, 'Sáb' => 6, 'Dom' => 0] as $label => $val)
                                                            <label class="inline-flex items-center bg-gray-800 px-2 py-1 rounded border border-gray-600 cursor-pointer hover:border-aurora-cyan">
                                                                <input type="checkbox" id="day_{{ $credential->id }}_{{ $val }}" name="scheduled_days[]" value="{{ $val }}"
                                                                    class="form-checkbox text-aurora-cyan focus:ring-aurora-cyan bg-gray-700 border-gray-500 rounded">
                                                                <span class="ml-2 text-xs text-gray-300">{{ $label }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-400 mb-2">Horario</label>
                                                    <input type="time" id="sched_time_{{ $credential->id }}" name="scheduled_time"
                                                        class="bg-gray-800 border border-gray-600 text-white rounded p-1 text-sm">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex justify-end gap-2 pt-2">
                                            <button @click="isEditing = false" type="button" class="px-3 py-1 text-gray-400 hover:text-white transition">Cancelar</button>
                                            <button type="submit" class="px-4 py-1.5 bg-aurora-cyan text-gray-900 font-bold rounded hover:bg-cyan-400 transition shadow-lg shadow-cyan-500/20">
                                                Guardar Cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                {{-- FORMULARIO DE NUEVA INTEGRACIÓN (AlpineJS) --}}
                <x-card x-data="{ 
                    selectedService: '', 
                    services: {{ Js::from($apiServices) }},
                    get currentService() { 
                        return this.services.find(s => s.id == this.selectedService) 
                    } 
                }">
                    <x-slot name="header">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-plus-circle text-xl text-aurora-pink"></i>
                            <h3 class="font-headings text-xl text-gray-900">Nueva Integración</h3>
                        </div>
                    </x-slot>

                    <form action="{{ route('clients.credentials.store', $client) }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="api_service_id" value="Seleccionar Servicio" />
                            <select x-model="selectedService" name="api_service_id" id="api_service_id"
                                class="mt-1 block w-full bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-aurora-cyan focus:border-aurora-cyan p-2.5">
                                <option value="">-- Elige un servicio --</option>
                                <template x-for="service in services" :key="service.id">
                                    <option :value="service.id" x-text="service.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Campos dinámicos --}}
                        <div x-show="selectedService" x-transition class="border-t border-gray-200 pt-4 mt-4 space-y-4">
                            <div
                                class="bg-blue-50 border border-blue-200 p-3 rounded text-sm text-blue-800 mb-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Ingresa las credenciales para <span class="font-bold"
                                    x-text="currentService ? currentService.name : ''"></span>.
                            </div>

                            <template x-for="field in currentService.required_fields" :key="field">
                                <div>
                                    <label :for="'cred_' + field"
                                        class="block font-medium text-sm text-gray-700 mb-1 capitalize"
                                        x-text="field.replace(/_/g, ' ')"></label>
                                    <input type="text" :name="'credentials[' + field + ']'" :id="'cred_' + field"
                                        class="w-full bg-white border border-gray-300 rounded text-gray-900 p-2 focus:border-aurora-cyan focus:ring-1 focus:ring-aurora-cyan outline-none"
                                        required placeholder="...">
                                </div>
                            </template>
                        </div>
                        </template>

                        <div class="space-y-4 pt-4 border-t border-gray-200">
                            <h4 class="font-bold text-gray-900 mb-2">Automatización</h4>
                            <div>
                            <div x-data="{ autoMode: 'manual' }">
                                <div class="flex items-center space-x-4 mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" x-model="autoMode" name="automation_type" value="manual"
                                            class="form-radio text-aurora-cyan focus:ring-aurora-cyan bg-gray-900 border-gray-700">
                                        <span class="ml-2 text-gray-900">Manual</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" x-model="autoMode" name="automation_type" value="custom"
                                            class="form-radio text-aurora-cyan focus:ring-aurora-cyan bg-gray-900 border-gray-700">
                                        <span class="ml-2 text-gray-900">Automático</span>
                                    </label>
                                </div>

                                <div x-show="autoMode === 'custom'" x-transition class="bg-gray-50 p-4 rounded-lg border border-gray-200 space-y-4">
                                    
                                    {{-- Selección de Días --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Días de Ejecución</label>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(['Lun' => 1, 'Mar' => 2, 'Mié' => 3, 'Jue' => 4, 'Vie' => 5, 'Sáb' => 6, 'Dom' => 0] as $label => $val)
                                                <label class="inline-flex items-center bg-white px-3 py-1 rounded border border-gray-300 cursor-pointer hover:border-aurora-cyan">
                                                    <input type="checkbox" name="scheduled_days[]" value="{{ $val }}"
                                                        class="form-checkbox text-aurora-cyan focus:ring-aurora-cyan bg-gray-100 border-gray-300 rounded">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Selección de Hora --}}
                                    <div>
                                        <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">Horario</label>
                                        <input type="time" name="scheduled_time" id="scheduled_time"
                                            class="bg-white border border-gray-300 text-gray-900 rounded-lg focus:ring-aurora-cyan focus:border-aurora-cyan p-2">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="alert_email" value="Email de Alertas (Opcional)" />
                                <x-text-input id="alert_email" name="alert_email" type="email" class="mt-1 block w-full"
                                    placeholder="ej: contador@empresa.com" />
                                <p class="text-xs text-gray-400 mt-1">Si se deja vacío, se usarán las alertas globales.
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-primary-button>
                                <i class="fas fa-save mr-2"></i> Guardar Credenciales
                            </x-primary-button>
                        </div>
            </div>

            </form>
            </x-card>

                {{-- SECCIÓN DE MOVIMIENTOS (Transactions) --}}
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-headings text-xl text-light-text flex items-center">
                            <i class="fas fa-money-bill-wave text-aurora-green mr-2"></i> Movimientos
                        </h3>
                        <button class="bg-brand-dark/50 hover:bg-brand-dark text-white text-sm px-3 py-1 rounded border border-white/10 transition">
                            <i class="fas fa-file-excel mr-1 text-green-400"></i> Exportar Excel
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Origen</th>
                                    <th class="px-6 py-3">Descripción</th>
                                    <th class="px-6 py-3">Monto</th>
                                    <th class="px-6 py-3">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($client->transactions()->latest('date_at')->take(50)->get() as $tx)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-mono">
                                            {{ $tx->date_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2 py-0.5 rounded border border-gray-200">
                                                {{ $tx->apiService->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-900 font-medium">
                                            {{ $tx->description }}
                                        </td>
                                        <td class="px-6 py-4 font-bold {{ $tx->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $tx->type === 'income' ? '+' : '-' }} ${{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($tx->status === 'verified')
                                                <span class="text-green-500"><i class="fas fa-check-circle"></i> Verificado</span>
                                            @elseif($tx->status === 'rejected')
                                                <span class="text-red-500"><i class="fas fa-times-circle"></i> Rechazado</span>
                                            @else
                                                <span class="text-gray-400"><i class="fas fa-clock"></i> Pendiente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            No hay movimientos registrados aún.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

        </div>

    </div>
    </div>
</x-app-layout>