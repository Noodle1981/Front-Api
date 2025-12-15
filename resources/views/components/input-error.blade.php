{{-- resources/views/components/input-error.blade.php --}}
@props(['messages'])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'mt-2 text-sm text-red-400 flex items-start space-x-2']) }}>
        
        {{-- Icono de Alerta para captar la atención --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>

        <ul class="space-y-1">
            @foreach ((array) $messages as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif