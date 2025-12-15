@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-white/20 bg-white/50 text-white focus:border-white focus:ring-white rounded-md shadow-sm']) !!}>
{{-- ^^^ Clases ajustadas: Borde blanco sutil, fondo semitransparente, texto blanco, focus blanco --}}