{{-- resources/views/components/card.blade.php --}}
@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl shadow-lg text-white']) }}>
    @if ($title || isset($header))
        <div class="px-6 py-4 border-b border-white/10">
            @if ($title)
                <h3 class="font-headings text-xl text-white">{{ $title }}</h3>
            @else
                {{ $header }}
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="px-6 py-4 bg-white/10 border-t border-white/10 rounded-b-2xl">
            {{ $footer }}
        </div>
    @endif
</div>