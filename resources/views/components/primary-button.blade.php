<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-transparent rounded-md font-semibold text-xs text-[#166264] uppercase tracking-widest hover:bg-gray-100 focus:bg-gray-100 active:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-[#166264] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{-- ^^^ Clases ajustadas: Fondo blanco, texto naranja, hover/focus en tonos grises para contraste --}}
    {{ $slot }}
</button>