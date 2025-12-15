<x-app-layout>
    <x-slot name="header">
        Editar Cliente: {{ $client->name }}
    </x-slot>
    
    <x-card class="max-w-4xl mx-auto">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')
            @include('clients._form', ['btnText' => 'Actualizar Cliente'])
        </form>
    </x-card>
</x-app-layout>