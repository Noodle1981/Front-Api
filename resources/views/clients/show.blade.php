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

            </div>
        </div>
    </div>
</x-app-layout>

        </div>

    </div>
    </div>
</x-app-layout>