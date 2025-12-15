{{-- resources/views/components/session-status.blade.php --}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge([
        'class' => 'font-medium text-sm text-dark-void bg-gradient-to-r from-aurora-cyan/80 to-green-400/80
                    backdrop-blur-sm border border-aurora-cyan/50 rounded-lg shadow-lg
                    px-4 py-3 flex items-center space-x-3'
    ]) }}>
        {{-- Icono de Font Awesome para dar feedback visual inmediato --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-dark-void flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>

        <span>
            {{ $status }}
        </span>
    </div>
@endif