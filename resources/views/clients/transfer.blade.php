<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-exchange-alt mr-2 text-brand-dark"></i> Transferir Cliente
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <h3 class="text-lg font-bold text-gray-800 mb-4">Reasignación de Cartera</h3>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                    <p class="text-sm text-blue-700">
                        Estás a punto de transferir el cliente <strong>{{ $client->company ?? $client->fantasy_name }}</strong> ({{ $client->cuit }}).
                        El nuevo usuario tendrá acceso total y el usuario actual perderá el acceso.
                    </p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Contador Actual</label>
                    <div class="mt-1 p-2 bg-gray-100 rounded border border-gray-300 text-gray-600">
                        {{ $client->user->name ?? 'Sin asignar' }} ({{ $client->user->email ?? '-' }})
                    </div>
                </div>

                <form action="{{ route('programmer.clients.transfer.update', $client) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">Nuevo Contador Responsable</label>
                        <select name="user_id" id="user_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-brand-accent focus:border-brand-accent sm:text-sm rounded-md" required>
                            <option value="">-- Seleccionar Nuevo Usuario --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700">Motivo (Opcional)</label>
                        <textarea name="reason" id="reason" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-brand-accent focus:border-brand-accent sm:text-sm" placeholder="Ej: Licencia médica, redistribución de carga..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('programmer.clients.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-brand-dark text-white rounded hover:bg-gray-800 transition">
                            Confirmar Transferencia
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
