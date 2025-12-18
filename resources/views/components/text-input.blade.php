@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300/50 bg-white/60 backdrop-blur-md text-gray-900 placeholder-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>
{{-- ^^^ Clases ajustadas: Borde blanco sutil, fondo semitransparente, texto blanco, focus blanco --}}