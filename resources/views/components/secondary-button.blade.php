{{-- resources/views/components/secondary-button.blade.php --}}
<button {{ $attributes->merge([
    'type' => 'button', // Cambiado el type por defecto a 'button'
    'class' => 'inline-flex items-center justify-center px-5 py-2.5
                font-semibold text-xs text-aurora-cyan uppercase tracking-widest
                bg-aurora-cyan/10 border border-aurora-cyan/50 rounded-lg
                backdrop-blur-sm
                transition-all duration-300 ease-in-out
                hover:bg-aurora-cyan/20 hover:border-aurora-cyan/70 hover:shadow-md hover:shadow-aurora-cyan/20
                focus:outline-none focus:ring-2 focus:ring-aurora-cyan focus:ring-offset-2 focus:ring-offset-dark-void'
]) }}>
    {{ $slot }}
</button>