<x-app-layout>
    <x-slot name="header">
        Crear Nuevo Cliente
    </x-slot>
    
    <x-card class="max-w-4xl mx-auto">
        @if(session('error'))
            <div class="mb-6">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            @include('clients._form', [
                'client' => new \App\Models\Client,
                'btnText' => 'Guardar Cliente'
            ])
        </form>
    </x-card>
</x-app-layout>