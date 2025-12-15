<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-2 bg-gradient-to-r from-btn-start to-btn-end hover:to-btn-start text-white border border-transparent rounded-full font-bold text-sm uppercase tracking-widest shadow-[0_4px_14px_0_rgba(247,131,143,0.39)] transition-all ease-in-out duration-300 transform hover:scale-105']) }}>
    {{ $slot }}
</button>