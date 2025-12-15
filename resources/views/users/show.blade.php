<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <p class="font-semibold">Nombre:</p>
                        <p>{{ $user->name }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="font-semibold">Email:</p>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="font-semibold">Rol:</p>
                        <p>
                            @if ($user->is_admin)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Admin</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Usuario</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-4">
                        <p class="font-semibold">Comisión:</p>
                        <p>{{ $user->settings['commission_rate'] ?? 0 }}%</p>
                    </div>
                    @if (auth()->id() === $user->id)
                        <div class="mt-6">
                            <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:text-indigo-900">Editar mi perfil</a>
                        </div>
                    @elseif (auth()->user()->is_admin)
                        <div class="mt-6">
                            <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Editar usuario (Admin)</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
