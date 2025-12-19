<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-magic mr-2 text-aurora-cyan"></i> {{ __('Nueva API / Integración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8 text-center">
                <h3 class="text-2xl font-bold text-brand-dark mb-2">Selecciona un Proveedor</h3>
                <p class="text-gray-500">¿Con qué servicio deseas conectar a tu cliente hoy?</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($providers as $provider)
                    <a href="{{ route('programmer.integrations.configure', $provider) }}" 
                       class="group bg-white rounded-xl shadow-lg border border-gray-100 p-8 flex flex-col items-center hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                        
                        <div class="absolute inset-0 bg-gradient-to-br from-transparent to-gray-50 opacity-0 group-hover:opacity-100 transition duration-500"></div>

                        <div class="w-32 h-32 mb-6 flex items-center justify-center relative z-10">
                            @if($provider->logo_url)
                                <img src="{{ asset($provider->logo_url) }}" alt="{{ $provider->name }}" class="max-w-full max-h-full object-contain filter grayscale group-hover:grayscale-0 transition duration-300">
                            @else
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-4xl text-gray-400">
                                    <i class="fas fa-cube"></i>
                                </div>
                            @endif
                        </div>

                        <h4 class="text-xl font-bold text-gray-800 group-hover:text-aurora-cyan transition z-10">{{ $provider->name }}</h4>
                        <p class="text-sm text-gray-500 mt-2 text-center z-10">{{ $provider->endpoints_count ?? 0 }} endpoints disponibles</p>
                        
                        <div class="mt-6 px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold group-hover:bg-aurora-cyan group-hover:text-white transition z-10">
                            Conectar <i class="fas fa-arrow-right ml-2"></i>
                        </div>
                    </a>
                @endforeach
                
                {{-- Custom Option links to ?mode=manual --}}
                <a href="{{ route('programmer.apis.create', ['mode' => 'manual']) }}" 
                   class="group bg-gray-50 rounded-xl shadow border-2 border-dashed border-gray-300 p-8 flex flex-col items-center hover:border-aurora-cyan hover:bg-white transition-all duration-300">
                    <div class="w-20 h-20 mb-6 bg-gray-200 rounded-full flex items-center justify-center text-4xl text-gray-400 group-hover:text-aurora-cyan group-hover:bg-cyan-50 transition">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-600 group-hover:text-aurora-cyan transition">Crear API Personalizada</h4>
                    <p class="text-sm text-gray-500 mt-2 text-center">Define tu propio servicio</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
