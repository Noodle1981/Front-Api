{{-- resources/views/components/kpi-card.blade.php --}}
@props(['title', 'value', 'icon'])

<div {{ $attributes->merge(['class' => 'bg-white border border-gray-200 rounded-lg shadow-sm p-6 flex items-center space-x-4']) }}>
    <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center rounded-full bg-primary/10 text-primary">
        <i class="{{ $icon }} text-2xl"></i>
    </div>
    <div>
        <h3 class="text-sm font-medium text-text-muted uppercase tracking-wider">{{ $title }}</h3>
        <p class="text-3xl font-bold text-text-main mt-1">{{ $value }}</p>
    </div>
</div>