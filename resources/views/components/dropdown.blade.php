{{-- resources/views/components/dropdown.blade.php --}}
@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'p-2' // Modificamos el valor por defecto para añadir padding
])

@php
$alignmentClasses = match ($align) {
    'left' => 'origin-top-left start-0',
    'top' => 'origin-top',
    default => 'origin-top-right end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56', // Añadimos más opciones si las necesitas
    '64' => 'w-64',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-2xl shadow-2xl {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        
        {{-- Aquí está la magia del "Aurora Glass" --}}
    <div class="rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 text-white {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>