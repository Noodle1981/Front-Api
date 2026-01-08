<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 pt-6 mb-12">
        <!-- Premium Header -->
        <div class="bg-gradient-to-r from-brand-dark to-brand-light rounded-2xl shadow-xl p-8 text-white overflow-hidden relative border-b-4 border-brand-accent/30 mb-6">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black mb-1 flex items-center">
                        <i class="fas fa-edit mr-3 text-brand-accent"></i> MODIFICAR CLIENTE
                    </h2>
                    <p class="text-blue-100 opacity-80 font-medium">Actualización de parámetros, contactos y jerarquía comercial.</p>
                </div>
                
                <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl text-white font-bold text-xs uppercase hover:bg-white/20 transition-all border border-white/20 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> CANCELAR
                </a>
            </div>
            <!-- Decoración -->
            <i class="fas fa-pen-nib absolute right-[-20px] top-[-10px] text-white/5 text-9xl rotate-12"></i>
        </div>
    
    <x-card class="max-w-4xl mx-auto">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            @include('clients._form', ['btnText' => 'Actualizar Cliente'])
        </form>
    </x-card>
</x-app-layout>