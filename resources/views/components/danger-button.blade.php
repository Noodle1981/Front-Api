{{-- resources/views/components/danger-button.blade.php --}}
<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center justify-center px-5 py-2.5 font-semibold text-xs text-white uppercase tracking-widest bg-red-600/90 border border-red-600/50 rounded-lg shadow-md backdrop-blur-sm transition-all duration-300 ease-in-out hover:bg-red-600 hover:shadow-lg hover:shadow-red-600/40 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-background'
]) }}>
    {{-- Opcional: Añadir un icono para reforzar la acción --}}
    {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
    </svg> --}}
    {{ $slot }}
</button>