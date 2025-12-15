<x-app-layout>
    <x-slot name="header">
        Inscribir a {{ $contact->name }} en una Secuencia
    </x-slot>

    <x-card class="max-w-3xl mx-auto">
        <form action="{{ route('contacts.enroll.store', $contact) }}" method="POST">
            @csrf
            
            {{-- Párrafo descriptivo estilizado --}}
            <div class="mb-8 p-4 border border-white/10 rounded-lg bg-gray-800/50">
                <p class="text-light-text-muted">
                    Estás a punto de inscribir a <strong class="text-light-text">{{ $contact->name }}</strong> (de <strong class="text-light-text">{{ $contact->client->name }}</strong>) en una secuencia de seguimiento automático.
                </p>
            </div>

            {{-- Campo de selección de secuencia --}}
            <div class="mb-6">
                <x-input-label for="sequence_id" value="Selecciona la Secuencia" />
                <select name="sequence_id" id="sequence_id" class="mt-1 block w-full bg-gray-900/60 border border-white/10 rounded-lg text-light-text transition-all duration-300 focus:border-aurora-cyan focus:ring-2 focus:ring-aurora-cyan/40 focus:outline-none" required>
                    <option value="" disabled selected>-- Elige una plantilla de seguimiento --</option>
                    @foreach ($sequences as $sequence)
                        <option value="{{ $sequence->id }}">{{ $sequence->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('sequence_id')" class="mt-2" />
            </div>
            
            {{-- Botones de Acción --}}
            <div class="flex items-center justify-end mt-8 space-x-4">
                <a href="{{ route('clients.show', $contact->client) }}">
                    <x-secondary-button type="button">
                        Cancelar
                    </x-secondary-button>
                </a>

                {{-- Botón de "Éxito" o "Acción Positiva" --}}
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-full font-bold text-sm text-dark-void uppercase tracking-widest
                                            bg-gradient-to-r from-green-400 to-aurora-cyan
                                            transition-all duration-300 ease-in-out transform hover:scale-105 hover:shadow-lg hover:shadow-green-500/40
                                            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 focus:ring-offset-dark-void active:scale-100">
                    <i class="fas fa-play mr-2"></i>
                    Iniciar Secuencia
                </button>
            </div>
        </form>
    </x-card>
</x-app-layout>