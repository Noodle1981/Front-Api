{{-- resources/views/components/smart-date.blade.php --}}
@props([
    'date',
    'format' => 'd M, Y', // Formato por defecto: "25 Ago, 2024"
    'human' => false, // ¿Usar formato "hace 2 días"?
    'withIcon' => false,
    'withColor' => false, // ¿Aplicar colores para fechas vencidas?
])

@php

    $carbonDate = null;
    // Si no hay fecha, no mostramos nada.
    if (!$date) {
        $date = null;
    } else {
        // Nos aseguramos de que sea un objeto Carbon.
        $carbonDate = \Carbon\Carbon::parse($date);
    }

    $displayText = '';
    $tooltipText = '';
    $colorClass = 'text-light-text-muted'; // Color por defecto

    if ($carbonDate) {
        $tooltipText = $carbonDate->format('d/m/Y H:i:s'); // Tooltip siempre tiene la fecha completa

        if ($human) {
            $displayText = $carbonDate->diffForHumans();
        } else {
            $displayText = $carbonDate->translatedFormat($format);
        }

        if ($withColor) {
            if ($carbonDate->isPast() && !$carbonDate->isToday()) {
                $colorClass = 'text-aurora-red-pop'; // Vencido
            } elseif ($carbonDate->isToday()) {
                $colorClass = 'text-yellow-400'; // Para hoy
            } elseif ($carbonDate->isFuture()) {
                $colorClass = 'text-green-400'; // Futuro
            }
        }
    }
@endphp

@if ($carbonDate)
    <span title="{{ $tooltipText }}" @class(['inline-flex items-center text-sm', $colorClass])>
        @if($withIcon)
            <i class="far fa-calendar-alt mr-1.5"></i>
        @endif
        {{ $displayText }}
    </span>
@else
    <span class="text-sm text-light-text-muted/50">N/A</span>
@endif