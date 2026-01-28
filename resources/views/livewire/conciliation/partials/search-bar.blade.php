<div class="flex flex-wrap gap-4 mb-4 items-end">
    <!-- Single Date Filter -->
    @if(isset($filterOptions['fechas']) && count($filterOptions['fechas']) > 0)
        <div>
            <label class="block text-xs text-gray-500 mb-1">Fecha</label>
            <select
                wire:model.live="filterFecha"
                class="block w-40 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
            >
                <option value="">Todas</option>
                @foreach($filterOptions['fechas'] as $fecha)
                    <option value="{{ $fecha->format('Y-m-d') }}">{{ $fecha->format('d/m/Y') }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Turno Filter -->
    @if(isset($filterOptions['turnos']) && count($filterOptions['turnos']) > 0)
        <div>
            <label class="block text-xs text-gray-500 mb-1">Turno</label>
            <select
                wire:model.live="filterTurno"
                class="block w-40 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
            >
                <option value="">Todos</option>
                @foreach($filterOptions['turnos'] as $turno)
                    <option value="{{ $turno }}">{{ $turno }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Estado Filter -->
    @if(isset($estados) && count($estados) > 0)
        <div>
            <label class="block text-xs text-gray-500 mb-1">Estado</label>
            <select
                wire:model.live="filterEstado"
                class="block w-44 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
            >
                <option value="">Todos</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado }}">{{ $estado }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Metodo Pago Filter -->
    @if(isset($metodosPago) && count($metodosPago) > 0)
        <div>
            <label class="block text-xs text-gray-500 mb-1">Metodo Pago</label>
            <select
                wire:model.live="filterMetodoPago"
                class="block w-44 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
            >
                <option value="">Todos</option>
                @foreach($metodosPago as $metodo)
                    <option value="{{ $metodo }}">{{ $metodo }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Tipo Filter (for Caja) -->
    @if(isset($tiposCaja) && count($tiposCaja) > 0)
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tipo</label>
            <select
                wire:model.live="filterTipo"
                class="block w-36 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
            >
                <option value="">Todos</option>
                @foreach($tiposCaja as $tipo)
                    <option value="{{ $tipo }}">{{ $tipo }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Search Input -->
    <div class="flex-1 min-w-48">
        <label class="block text-xs text-gray-500 mb-1">Buscar</label>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar..."
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"
        >
    </div>

    <!-- Clear Filters Button -->
    @if($search || $filterTurno || $filterEstado || $filterMetodoPago || $filterTipo || $filterFecha)
        <button
            wire:click="clearFilters"
            type="button"
            class="px-3 py-2 text-sm text-purple-600 hover:text-purple-800 self-end"
        >
            <i class="fas fa-times mr-1"></i> Limpiar
        </button>
    @endif
</div>
