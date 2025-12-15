<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">


            <span>Detalles del Cliente: {{ $client->name }}</span>
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
                        <p><strong>Dirección Fiscal:</strong> <span
                                class="text-light-text">{{ $client->fiscal_address_street ?? 'N/A' }}</span></p>
                        <p><strong>Actividad Económica:</strong> <span
                                class="text-light-text">{{ $client->economic_activity ?? 'N/A' }}</span></p>
                        <p><strong>ART:</strong> <span
                                class="text-light-text">{{ $client->art_provider ?? 'N/A' }}</span></p>
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
                        @if($client->notes)
                            <div class="pt-3 border-t border-white/10">
                                <p><strong>Notas:</strong></p>
                                <p class="text-light-text whitespace-pre-wrap">{{ $client->notes }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            </div>

        </div>
    </div>
</x-app-layout>