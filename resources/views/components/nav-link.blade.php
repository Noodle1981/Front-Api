@props(['active'])

@php
    // Sidebar Style Classes
    $classes = ($active ?? false)
        ? 'group flex items-center px-4 py-3 text-sm font-bold bg-white/10 text-brand-accent rounded-lg transition-all duration-200 shadow-[0_0_15px_rgba(254,145,146,0.1)]' // Active
        : 'group flex items-center px-4 py-3 text-sm font-medium text-gray-400 rounded-lg hover:text-white hover:bg-white/5 transition-all duration-200'; // Inactive
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>