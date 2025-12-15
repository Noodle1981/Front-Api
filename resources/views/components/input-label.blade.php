@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-white']) }}> {{-- Texto de la etiqueta en blanco --}}
    {{ $value ?? $slot }}
</label>