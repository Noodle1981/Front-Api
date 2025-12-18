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
                            <h3 class="font-headings text-xl text-light-text">Información Fiscal</h3>
                        </div>
                    </x-slot>
                    <div class="space-y-3 text-light-text-muted">
                        <p><strong>Razón Social:</strong> <span
                                class="text-light-text">{{ $client->company ?? 'N/A' }}</span></p>
                        <p><strong>CUIT:</strong> <span class="text-light-text">{{ $client->cuit ?? 'N/A' }}</span></p>
                        @if($client->fantasy_name)
                            <p><strong>Nombre Fantasía:</strong> <span
                                    class="text-light-text">{{ $client->fantasy_name }}</span></p>
                        @endif
                        <p><strong>Condición Fiscal:</strong> <span
                                class="text-light-text">{{ $client->tax_condition ?? 'N/A' }}</span></p>

                        <!-- Nuevos Campos Stage 6 -->
                        <p><strong>Rubro:</strong> <span class="text-light-text">{{ $client->industry ?? '-' }}</span>
                        </p>
                        <p><strong>Empleados:</strong> <span
                                class="text-light-text">{{ $client->employees_count ?? '-' }}</span></p>

                        <div class="border-t border-white/10 pt-2 mt-2">
                            <p class="font-bold text-aurora-cyan mb-1">Dirección:</p>
                            <span class="text-light-text block">{{ $client->address ?? '-' }}</span>
                            <span class="text-light-text block">
                                {{ $client->city }}{{ $client->city && $client->state ? ',' : '' }} {{ $client->state }}
                            </span>
                            @if($client->zip_code)
                                <span class="text-light-text block">CP: {{ $client->zip_code }}</span>
                            @endif
                        </div>
                    </div>
                </x-card>

                <!-- Tarjeta de Información de Contacto -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-info-circle text-xl text-aurora-cyan"></i>
                            <h3 class="font-headings text-xl text-light-text">Información de Contacto</h3>
                        </div>
                    </x-slot>
                    <div class="space-y-3 text-light-text-muted">
                        <p><strong>Email:</strong> <span class="text-light-text">{{ $client->email ?? 'N/A' }}</span>
                        </p>
                        <div class="flex justify-between items-center">
                            <p><strong>Teléfono:</strong> <span
                                    class="text-light-text">{{ $client->phone ?? 'N/A' }}</span></p>

                            @if ($client->phone)
                                <a href="https://wa.me/{{ $client->phone }}" target="_blank"
                                    class="text-green-400 hover:text-green-300 transition text-2xl"
                                    title="Contactar por WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            @endif
                        </div>
                        @if($client->internal_notes)
                            <div class="pt-3 border-t border-white/10">
                                <p><strong>Notas Internas:</strong></p>
                                <p class="text-light-text whitespace-pre-wrap">{{ $client->internal_notes }}</p>
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
                                <div
                                    class="bg-gray-800/50 p-4 rounded-lg border border-white/10 flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-brand-dark/50 flex items-center justify-center text-aurora-cyan border border-aurora-cyan/30">
                                            <i class="fas fa-server"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-white">{{ $credential->apiService->name }}</h4>
                                            <span
                                                class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full">Activo</span>
                                        </div>
                                    </div>
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
                            <h3 class="font-headings text-xl text-light-text">Nueva Integración</h3>
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
                        <div x-show="selectedService" x-transition class="border-t border-white/10 pt-4 mt-4 space-y-4">
                            <div
                                class="bg-blue-900/20 border border-blue-500/20 p-3 rounded text-sm text-blue-200 mb-4">
                                <i class="fas fa-info-circle mr-1"></i>
                                Ingresa las credenciales para <span class="font-bold"
                                    x-text="currentService ? currentService.name : ''"></span>.
                            </div>

                            <template x-for="field in currentService.required_fields" :key="field">
                                <div>
                                    <label :for="'cred_' + field"
                                        class="block font-medium text-sm text-gray-300 mb-1 capitalize"
                                        x-text="field.replace(/_/g, ' ')"></label>
                                    <input type="text" :name="'credentials[' + field + ']'" :id="'cred_' + field"
                                        class="w-full bg-gray-800 border border-gray-600 rounded text-white p-2 focus:border-aurora-cyan focus:ring-1 focus:ring-aurora-cyan outline-none"
                                        required placeholder="...">
                                </div>
                            </template>
                        </div>
                        </template>

                        <div class="space-y-4 pt-4 border-t border-white/10">
                            <h4 class="font-bold text-white mb-2">Automatización</h4>
                            <div>
                                <x-input-label for="execution_frequency" value="Frecuencia de Ejecución" />
                                <select name="execution_frequency" id="execution_frequency"
                                    class="mt-1 block w-full bg-gray-900 border border-gray-700 text-white rounded-lg focus:ring-aurora-cyan focus:border-aurora-cyan p-2.5">
                                    <option value="">Manual (Sin automatización)</option>
                                    <option value="daily_0800">Diario - 08:00 AM</option>
                                    <option value="daily_0900">Diario - 09:00 AM</option>
                                    <option value="daily_1200">Diario - 12:00 PM</option>
                                    <option value="weekly_mon_0900">Semanal (Lunes 09:00)</option>
                                    <option value="monthly_1st_0900">Mensual (Día 1 09:00)</option>
                                </select>
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

        </div>

    </div>
    </div>
</x-app-layout>