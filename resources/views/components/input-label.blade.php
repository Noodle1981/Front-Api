@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-900']) }}> {{-- Texto de la etiqueta en
    NEGRO --}}
    {{ $value ?? $slot }}
</label>