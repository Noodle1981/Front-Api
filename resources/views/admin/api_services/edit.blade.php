<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <span>{{ __('Editar Servicio API') }}</span>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <x-card>
            <form action="{{ route('admin.api-services.update', $apiService) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Nombre -->
                <div>
                    <x-input-label for="name" value="Nombre del Servicio" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full text-black"
                        placeholder="Ej: Mercado Pago" value="{{ old('name', $apiService->name) }}" required
                        autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Slug (Opcional) -->
                <div>
                    <x-input-label for="slug" value="Slug / Identificador" />
                    <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full text-black"
                        placeholder="mercado-pago" value="{{ old('slug', $apiService->slug) }}" />
                    <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                </div>

                <!-- Base URL -->
                <div>
                    <x-input-label for="base_url" value="URL Base de la API (Opcional)" />
                    <x-text-input id="base_url" name="base_url" type="url" class="mt-1 block w-full text-black"
                        placeholder="https://api.mercadopago.com/v1"
                        value="{{ old('base_url', $apiService->base_url) }}" />
                    <x-input-error :messages="$errors->get('base_url')" class="mt-2" />
                </div>

                <!-- Campos Requeridos (Lista Dinámica) -->
                <div x-data="{ fields: {{ Js::from($apiService->required_fields) }} }"
                    class="border-t border-gray-200 pt-4 mt-6">
                    <h3 class="font-bold text-gray-700 mb-2">Campos Requeridos</h3>
                    <p class="text-sm text-gray-500 mb-4">Define qué datos (tokens, keys) se le pedirán al contador para
                        conectar este servicio.</p>

                    <div class="space-y-3">
                        <template x-for="(field, index) in fields" :key="index">
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <x-text-input :name="'required_fields[]'" type="text"
                                        class="block w-full text-black" placeholder="Ej: access_token"
                                        x-model="fields[index]" required />
                                </div>
                                <button type="button" class="text-red-500 hover:text-red-700 px-2"
                                    @click="fields.splice(index, 1)" x-show="fields.length > 1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="fields.push('')"
                        class="mt-3 text-sm text-aurora-pink hover:text-aurora-purple font-medium flex items-center">
                        <i class="fas fa-plus-circle mr-1"></i> Agregar otro campo
                    </button>

                    <x-input-error :messages="$errors->get('required_fields')" class="mt-2" />
                    <x-input-error :messages="$errors->get('required_fields.*')" class="mt-2" />
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('admin.api-services.index') }}"
                        class="px-4 py-2 mr-2 text-gray-600 hover:text-gray-900">
                        Cancelar
                    </a>
                    <x-primary-button>
                        Actualizar Servicio
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>