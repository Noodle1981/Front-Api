<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pt-6">
        <!-- Premium Header -->
        <div class="bg-gradient-to-r from-brand-dark to-brand-light rounded-2xl shadow-xl p-8 text-white overflow-hidden relative border-b-4 border-brand-accent/30 mb-6">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black mb-1 flex items-center">
                        <i class="fas fa-building mr-3 text-brand-accent"></i> {{ $client->company }}
                    </h2>
                    <p class="text-blue-100 opacity-80 font-medium">Visualización detallada de perfil, jerarquía y contactos.</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl text-white font-bold text-xs uppercase hover:bg-white/20 transition-all border border-white/20 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> LISTADO
                    </a>
                    <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-brand-accent to-yellow-400 hover:from-white hover:to-white text-brand-dark rounded-xl font-black text-xs uppercase tracking-widest shadow-lg transition-all transform hover:scale-105">
                        <i class="fas fa-pen mr-2"></i>
                        Editar
                    </a>
                </div>
            </div>
            <!-- Decoración -->
            <i class="fas fa-shield-alt absolute right-[-20px] top-[-10px] text-white/5 text-9xl rotate-12"></i>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">

            <!-- Columna Izquierda: Detalles y Contactos -->
            <div class="lg:col-span-1 space-y-8">

                <!-- ALERTA DE JERARQUÍA -->
                @if($client->parent)
                    <div class="bg-blue-600/10 border border-blue-500/20 p-6 rounded-2xl text-blue-900 relative overflow-hidden backdrop-blur-sm">
                        <div class="relative z-10">
                            <i class="fas fa-level-up-alt mr-2 text-xl text-blue-600"></i>
                            <span class="font-bold text-blue-600 uppercase text-[10px] tracking-widest block mb-1">Entidad Superior</span>
                            <a href="{{ route('clients.show', $client->parent) }}"
                                class="text-lg font-black hover:text-brand-dark transition-colors block">
                                {{ $client->parent->company }}
                            </a>
                            @if($client->branch_name)
                                <div class="mt-3 flex items-center text-xs font-bold text-gray-500 uppercase">
                                    <i class="fas fa-store mr-2"></i> Sucursal: <span class="ml-1 text-blue-600">{{ $client->branch_name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($client->children->isNotEmpty())
                    <div class="bg-purple-600/10 border border-purple-500/20 p-6 rounded-2xl text-purple-900">
                        <div class="font-black flex items-center mb-4 text-xs uppercase tracking-widest text-purple-600">
                            <i class="fas fa-network-wired mr-2"></i> Sucursales / Anexos ({{ $client->children->count() }})
                        </div>
                        <ul class="space-y-3">
                            @foreach($client->children as $child)
                                <li class="flex items-center group">
                                    <div class="w-1.5 h-1.5 rounded-full bg-purple-400 mr-3 group-hover:scale-150 transition-transform"></div>
                                    <a href="{{ route('clients.show', $child) }}"
                                        class="text-sm font-bold text-gray-700 hover:text-purple-600 hover:underline transition">
                                        {{ $child->branch_name ?? $child->fantasy_name ?? $child->company }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tarjeta de Información Fiscal -->
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                        <h3 class="font-black text-gray-700 text-[10px] uppercase tracking-[0.2em] flex items-center">
                            <i class="fas fa-building mr-2 text-brand-dark"></i> Info Fiscal
                        </h3>
                    </div>
                    <div class="p-6 space-y-4 text-sm">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Razón Social</p>
                            <p class="font-bold text-gray-900">{{ $client->company }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Identificación Fiscal</p>
                            <p class="font-bold text-brand-dark font-mono text-lg">{{ $client->cuit }}</p>
                        </div>
                        @if($client->fantasy_name)
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nombre Fantasía</p>
                                <p class="font-bold text-gray-900">{{ $client->fantasy_name }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Condición Frente al IVA</p>
                            <p class="font-bold text-gray-700">{{ $client->tax_condition ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-50 space-y-4">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Rubro</p>
                                <p class="font-bold text-gray-700">{{ $client->industry ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Dotación Empleados</p>
                                <p class="font-bold text-gray-700">{{ $client->employees_count ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Ubicación</p>
                            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                <span class="text-gray-900 font-bold block">{{ $client->address ?? '-' }}</span>
                                <span class="text-gray-500 font-medium text-xs block mt-1 uppercase">
                                    {{ $client->city }}{{ $client->city && $client->state ? ',' : '' }} {{ $client->state }}
                                </span>
                                @if($client->zip_code)
                                    <span class="text-xs text-gray-400 font-mono block mt-1">CP: {{ $client->zip_code }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de Información de Contacto -->
                <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                        <h3 class="font-black text-gray-700 text-[10px] uppercase tracking-[0.2em] flex items-center">
                            <i class="fas fa-info-circle mr-2 text-brand-dark"></i> Contactos
                        </h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Correo Electrónico</p>
                            <a href="mailto:{{ $client->email }}" class="font-black text-blue-600 hover:underline">{{ $client->email ?? 'N/A' }}</a>
                        </div>
                        <div class="flex justify-between items-center group">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Teléfono Central</p>
                                <p class="font-black text-gray-900">{{ $client->phone ?? 'N/A' }}</p>
                            </div>

                            @if ($client->phone)
                                <a href="https://wa.me/{{ $client->phone }}" target="_blank"
                                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all transform group-hover:scale-110 shadow-sm"
                                    title="Contactar por WhatsApp">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                </a>
                            @endif
                        </div>
                        @if($client->internal_notes)
                            <div class="pt-4 border-t border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones Internas</p>
                                <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 text-amber-900 text-xs font-medium italic leading-relaxed whitespace-pre-wrap">
                                    {{ $client->internal_notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Workflows / Actividad (Placeholder for future) -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white shadow-sm rounded-2xl p-12 border border-gray-100 flex flex-col items-center justify-center opacity-40">
                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                    <p class="font-black text-xs uppercase tracking-widest text-gray-400">Próximamente: Historial de Operaciones</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>