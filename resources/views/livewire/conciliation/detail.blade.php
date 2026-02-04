<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ $client->company ?? 'Detalle de Conciliacion' }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            @if($fechaInicio && $fechaFin)
                                Periodo: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                            @else
                                Todos los datos disponibles
                            @endif
                        </p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('programmer.conciliacion.index') }}" class="text-purple-600 hover:text-purple-900">
                            <i class="fas fa-building mr-1"></i> Ver empresas
                        </a>
                    </div>
                </div>

                <!-- Date Range Selector -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Date inputs -->
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Desde:</label>
                            <input type="date" wire:model.live="fechaInicio" wire:change="updateDateRange"
                                class="rounded-md border-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Hasta:</label>
                            <input type="date" wire:model.live="fechaFin" wire:change="updateDateRange"
                                class="rounded-md border-gray-300 shadow-sm text-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <!-- Quick select buttons -->
                        <div class="flex items-center gap-2 ml-4">
                            <button wire:click="selectCurrentMonth" type="button"
                                class="px-3 py-1.5 text-xs font-medium rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200">
                                Mes actual
                            </button>
                            <button wire:click="selectLastMonth" type="button"
                                class="px-3 py-1.5 text-xs font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
                                Mes anterior
                            </button>
                            <button wire:click="selectAllData" type="button"
                                class="px-3 py-1.5 text-xs font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">
                                Todo
                            </button>
                        </div>
                    </div>

                    <!-- Available months quick select -->
                    @if(!empty($availableMonths))
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <span class="text-xs text-gray-500 mr-2">Meses con datos:</span>
                            <div class="inline-flex flex-wrap gap-1 mt-1">
                                @foreach(array_slice($availableMonths, 0, 6) as $month)
                                    <button wire:click="selectMonth('{{ $month['fecha_inicio'] }}', '{{ $month['fecha_fin'] }}')"
                                        type="button"
                                        class="px-2 py-1 text-xs rounded-full transition-colors
                                            {{ $fechaInicio == $month['fecha_inicio'] && $fechaFin == $month['fecha_fin']
                                                ? 'bg-purple-600 text-white'
                                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                        {{ $month['label'] }} ({{ $month['count'] }})
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-4 overflow-x-auto">
                        @php
                            // Base tabs (always visible)
                            $tabs = [
                                'turnos' => 'Turnos',
                                'getnet' => 'Getnet',
                                'mp' => 'MercadoPago',
                                'sistema' => 'Resultados',
                                'caja' => 'Caja Adicion',
                                'devoluciones' => 'Devoluciones',
                                'mp_negativos' => 'Pagos MP',
                            ];

                            // Add arqueo tabs only if arqueo data exists
                            if ($hasArqueoData ?? false) {
                                $tabs = array_merge([
                                    'arqueo' => 'Arqueo por Turno',
                                    'resumen' => 'Resumen arqueo',
                                ], $tabs);
                            }
                        @endphp
                        @foreach($tabs as $key => $label)
                            <button
                                wire:click="setTab('{{ $key }}')"
                                class="whitespace-nowrap py-2 px-3 border-b-2 text-sm font-medium
                                    {{ $activeTab === $key
                                        ? 'border-purple-500 text-purple-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                {{ $label }}
                                <span class="ml-1 text-xs text-gray-400">({{ $counts[$key] ?? 0 }})</span>
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Tab Content -->
                @if($activeTab === 'arqueo')
                    <!-- Arqueo por Turno Tab - Full Table -->
                    @include('livewire.conciliation.partials.search-bar', ['estados' => $filterOptions['turnos'] ?? []])
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha', 'label' => 'Fecha'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'dia', 'label' => 'Dia'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'encargado', 'label' => 'Encargado'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'ventas_totales', 'label' => 'Ventas'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'cantidad_tickets', 'label' => 'Tickets'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'cantidad_comensales', 'label' => 'Comensales'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'ticket_promedio', 'label' => 'Ticket Prom.'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'mp_diferencia', 'label' => 'Dif. MP'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'getnet_diferencia', 'label' => 'Dif. Getnet'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'efectivo_diferencia', 'label' => 'Dif. Efectivo'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'efectivo_estado', 'label' => 'Estado'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($arqueos ?? [] as $arqueo)
                                    <tr class="{{ $arqueo->hasAlerts() ? 'bg-red-50' : '' }}">
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $arqueo->fecha?->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2">{{ $arqueo->dia }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $arqueo->turno === 'MANANA' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $arqueo->turno === 'TARDE' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $arqueo->turno === 'NOCHE' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                                {{ $arqueo->turno }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">{{ Str::limit($arqueo->encargado, 15) }}</td>
                                        <td class="px-3 py-2 text-right font-medium">${{ number_format($arqueo->ventas_totales, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-center">{{ $arqueo->cantidad_tickets }}</td>
                                        <td class="px-3 py-2 text-center">{{ $arqueo->cantidad_comensales }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format($arqueo->ticket_promedio, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-right {{ $arqueo->mp_diferencia != 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                            ${{ number_format($arqueo->mp_diferencia, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right {{ $arqueo->getnet_diferencia != 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                            ${{ number_format($arqueo->getnet_diferencia, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-right {{ $arqueo->efectivo_diferencia < 0 ? 'text-red-600 font-medium' : ($arqueo->efectivo_diferencia > 0 ? 'text-amber-600' : 'text-green-600') }}">
                                            ${{ number_format($arqueo->efectivo_diferencia, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($arqueo->efectivo_estado)
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    {{ $arqueo->efectivo_estado === 'OK' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $arqueo->efectivo_estado }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="12" class="px-3 py-8 text-center text-gray-500">No hay datos de arqueo</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $arqueos->links() }}</div>

                @elseif($activeTab === 'resumen')
                    <!-- Resumen Tab -->
                    <div class="space-y-4">
                        @forelse($summaries ?? [] as $summary)
                            <div class="border border-gray-200 rounded-lg p-4 {{ $summary->hasAlerts() ? 'bg-red-50 border-red-200' : '' }}">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-semibold text-lg">{{ $summary->fecha->format('d/m/Y') }} - {{ $summary->dia }}</h3>
                                        <p class="text-sm text-gray-500">Turno: {{ $summary->turno }} | Encargado: {{ $summary->encargado }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-900">${{ number_format($summary->ventas_totales, 0, ',', '.') }}</div>
                                        <div class="text-sm text-gray-500">{{ $summary->cantidad_tickets }} tickets | {{ $summary->cantidad_comensales }} comensales</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- MercadoPago -->
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <div class="text-sm font-medium text-blue-800 mb-2">MercadoPago</div>
                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Real:</span>
                                                <span class="font-medium">${{ number_format($summary->mp_ventas_real, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Conciliado:</span>
                                                <span class="font-medium text-green-600">${{ number_format($summary->mp_conciliado, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Diferencia:</span>
                                                <span class="font-medium {{ $summary->mp_diferencia != 0 ? 'text-red-600' : '' }}">${{ number_format($summary->mp_diferencia, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Getnet -->
                                    <div class="bg-green-50 rounded-lg p-3">
                                        <div class="text-sm font-medium text-green-800 mb-2">Getnet</div>
                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Real:</span>
                                                <span class="font-medium">${{ number_format($summary->getnet_ventas_real, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Conciliado:</span>
                                                <span class="font-medium text-green-600">${{ number_format($summary->getnet_conciliado, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Diferencia:</span>
                                                <span class="font-medium {{ $summary->getnet_diferencia != 0 ? 'text-red-600' : '' }}">${{ number_format($summary->getnet_diferencia, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Efectivo -->
                                    <div class="bg-yellow-50 rounded-lg p-3">
                                        <div class="text-sm font-medium text-yellow-800 mb-2">Efectivo</div>
                                        <div class="space-y-1 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total:</span>
                                                <span class="font-medium">${{ number_format($summary->efectivo_total, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Recuento:</span>
                                                <span class="font-medium">${{ number_format($summary->efectivo_recuento, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Diferencia:</span>
                                                <span class="font-medium {{ $summary->efectivo_diferencia < 0 ? 'text-red-600' : ($summary->efectivo_diferencia > 0 ? 'text-green-600' : '') }}">
                                                    ${{ number_format($summary->efectivo_diferencia, 0, ',', '.') }}
                                                    @if($summary->efectivo_estado)
                                                        <span class="text-xs">({{ $summary->efectivo_estado }})</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">No hay datos de resumen</div>
                        @endforelse
                    </div>

                @elseif($activeTab === 'getnet')
                    <!-- Getnet Tab -->
                    @include('livewire.conciliation.partials.search-bar', ['estados' => $estadosGetnet ?? []])
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha_operacion', 'label' => 'Fecha'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'cod_transaccion', 'label' => 'Transaccion'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'marca', 'label' => 'Tarjeta'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'monto_bruto', 'label' => 'Monto'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'estado_conciliacion', 'label' => 'Estado'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'tipo_match', 'label' => 'Match'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions ?? [] as $tx)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $tx->fecha_operacion?->format('d/m/Y H:i') }}</td>
                                        <td class="px-3 py-2">
                                            <div class="font-mono text-xs">{{ Str::limit($tx->cod_transaccion, 20) }}</div>
                                            <div class="text-gray-500 text-xs">Cupon: {{ $tx->nro_cupon }}</div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="font-medium">{{ ucfirst($tx->marca) }}</span>
                                            <span class="text-gray-500">****{{ $tx->tarjeta_ultimos4 }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium">${{ number_format($tx->monto_bruto, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2">{{ $tx->turno }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $tx->estado_conciliacion === 'Conciliado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $tx->estado_conciliacion ?? 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-500">{{ $tx->tipo_match }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-3 py-8 text-center text-gray-500">No hay transacciones</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $transactions->links() }}</div>

                @elseif($activeTab === 'mp')
                    <!-- MercadoPago Tab -->
                    @include('livewire.conciliation.partials.search-bar', ['estados' => $estadosMp ?? []])
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha', 'label' => 'Fecha'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'id_operacion_mp', 'label' => 'ID Operacion'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'monto_neto', 'label' => 'Monto'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'estado_conciliacion', 'label' => 'Estado'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'tipo_match', 'label' => 'Match'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions ?? [] as $tx)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $tx->fecha?->format('d/m/Y') }} {{ $tx->hora }}</td>
                                        <td class="px-3 py-2 font-mono text-xs">{{ $tx->id_operacion_mp }}</td>
                                        <td class="px-3 py-2 text-right font-medium">${{ number_format($tx->monto_neto, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2">{{ $tx->turno }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $tx->estado_conciliacion === 'Conciliado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $tx->estado_conciliacion ?? 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-500">{{ $tx->tipo_match }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">No hay transacciones</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $transactions->links() }}</div>

                @elseif($activeTab === 'sistema')
                    <!-- Sistema Tab -->
                    @include('livewire.conciliation.partials.search-bar', ['estados' => $estadosSistema ?? [], 'metodosPago' => $metodosPago ?? []])
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha_hora', 'label' => 'Fecha'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'id_ticket', 'label' => 'ID Ticket'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'monto_total', 'label' => 'Monto'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'metodo_pago', 'label' => 'Metodo Pago'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'estado_conciliacion', 'label' => 'Estado'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'conciliado', 'label' => 'Conciliado'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($sales ?? [] as $sale)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $sale->fecha_hora?->format('d/m/Y H:i') }}</td>
                                        <td class="px-3 py-2 font-mono text-xs">{{ $sale->id_ticket }}</td>
                                        <td class="px-3 py-2 text-right font-medium">${{ number_format($sale->monto_total, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2">{{ $sale->metodo_pago }}</td>
                                        <td class="px-3 py-2">{{ $sale->turno }}</td>
                                        <td class="px-3 py-2">
                                            @if($sale->estado_conciliacion)
                                                <span class="px-2 py-1 text-xs rounded-full {{ $sale->estado_conciliacion === 'Conciliado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $sale->estado_conciliacion }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($sale->conciliado)
                                                <span class="text-green-600"><i class="fas fa-check"></i> Si</span>
                                            @else
                                                <span class="text-red-600"><i class="fas fa-times"></i> No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-3 py-8 text-center text-gray-500">No hay ventas</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $sales->links() }}</div>

                @elseif($activeTab === 'caja')
                    <!-- Caja Tab -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-green-50 rounded-lg p-3">
                            <div class="text-sm text-green-600">Total Ingresos</div>
                            <div class="text-xl font-bold text-green-800">${{ number_format($totalIngresos ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-3">
                            <div class="text-sm text-red-600">Total Egresos</div>
                            <div class="text-xl font-bold text-red-800">${{ number_format($totalEgresos ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @include('livewire.conciliation.partials.search-bar', ['tiposCaja' => $tiposCaja ?? []])
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha_contable', 'label' => 'Fecha'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'tipo', 'label' => 'Tipo'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'proveedor_para', 'label' => 'Concepto'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'monto', 'label' => 'Monto'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'usuario', 'label' => 'Usuario'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($movements ?? [] as $mov)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $mov->fecha_contable?->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $mov->tipo === 'Ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $mov->tipo }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <div>{{ $mov->proveedor_para }}</div>
                                            <div class="text-gray-500 text-xs">{{ Str::limit($mov->comentario, 40) }}</div>
                                        </td>
                                        <td class="px-3 py-2 text-right font-medium {{ $mov->tipo === 'Ingreso' ? 'text-green-600' : 'text-red-600' }}">
                                            ${{ number_format($mov->monto, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2 text-xs">{{ Str::limit($mov->usuario, 20) }}</td>
                                        <td class="px-3 py-2">{{ $mov->turno }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-3 py-8 text-center text-gray-500">No hay movimientos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $movements->links() }}</div>

                @elseif($activeTab === 'turnos')
                    <!-- Turnos Tab -->
                    @include('livewire.conciliation.partials.search-bar')
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha_apertura', 'label' => 'Fecha'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'encargado', 'label' => 'Encargado'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'hora_apertura', 'label' => 'Apertura'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'hora_cierre', 'label' => 'Cierre'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'cantidad_comensales', 'label' => 'Comensales'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'apertura_caja', 'label' => 'Apertura Caja'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'recuento_efectivo', 'label' => 'Recuento'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($shifts ?? [] as $shift)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $shift->fecha_apertura?->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $shift->turno === 'MANANA' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $shift->turno === 'TARDE' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $shift->turno === 'NOCHE' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                                {{ $shift->turno }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">{{ $shift->encargado }}</td>
                                        <td class="px-3 py-2">{{ $shift->hora_apertura }}</td>
                                        <td class="px-3 py-2">{{ $shift->hora_cierre }}</td>
                                        <td class="px-3 py-2 text-center font-medium">{{ $shift->cantidad_comensales }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format($shift->apertura_caja, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format($shift->recuento_efectivo, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="px-3 py-8 text-center text-gray-500">No hay turnos registrados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $shifts->links() }}</div>

                @elseif($activeTab === 'devoluciones')
                    <!-- Devoluciones Tab -->
                    @include('livewire.conciliation.partials.search-bar')
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha_hora_pedido', 'label' => 'Fecha/Hora'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'producto', 'label' => 'Producto'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'precio', 'label' => 'Precio'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'comentario', 'label' => 'Comentario'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($refunds ?? [] as $refund)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ $refund->fecha_hora_pedido?->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-3 py-2 font-medium">{{ $refund->producto }}</td>
                                        <td class="px-3 py-2 text-right text-red-600">${{ number_format($refund->precio, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2 text-gray-500">{{ Str::limit($refund->comentario, 40) }}</td>
                                        <td class="px-3 py-2">{{ $refund->turno }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No hay devoluciones</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $refunds->links() }}</div>

                @elseif($activeTab === 'mp_negativos')
                    <!-- MP Negativos Tab -->
                    @include('livewire.conciliation.partials.search-bar')
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'fecha', 'label' => 'Fecha'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'hora', 'label' => 'Hora'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'id_operacion_mp', 'label' => 'ID Operacion'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'monto_neto', 'label' => 'Monto'])
                                    @include('livewire.conciliation.partials.sortable-header', ['field' => 'turno', 'label' => 'Turno'])
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($negatives ?? [] as $neg)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">{{ $neg->fecha?->format('d/m/Y') }}</td>
                                        <td class="px-3 py-2">{{ $neg->hora }}</td>
                                        <td class="px-3 py-2 font-mono text-xs">{{ $neg->id_operacion_mp }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-red-600">${{ number_format($neg->monto_neto, 0, ',', '.') }}</td>
                                        <td class="px-3 py-2">{{ $neg->turno }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No hay movimientos negativos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $negatives->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
