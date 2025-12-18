{{-- resources/views/components/secondary-button.blade.php --}}
<button {{ $attributes->merge([
    'type' => 'button', // Cambiado el type por defecto a 'button'
    'class' => 'inline-flex items-center justify-center px-5 py-2.5
                font-semibold text-xs text-gray-700 uppercase tracking-widest
                bg-white border border-gray-300 rounded-lg
                backdrop-blur-sm
                transition-all duration-300 ease-in-out
                hover:bg-pink-50 hover:text-pink-500 hover:border-pink-300 hover:shadow-md
                focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 focus:ring-offset-white'
]) }}>
    {{ $slot }}
</button>