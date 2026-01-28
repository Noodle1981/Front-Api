<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Conciliaciones por Empresa</h2>

                <!-- Global KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Empresas</div>
                        <div class="text-2xl font-bold">{{ $totalClientes }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Ventas Totales</div>
                        <div class="text-2xl font-bold">${{ number_format($totalVentasGlobal, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Turnos Procesados</div>
                        <div class="text-2xl font-bold">{{ number_format($totalTurnosGlobal) }}</div>
                    </div>
                    <div class="bg-gradient-to-r {{ $totalAlertas > 0 ? 'from-red-500 to-red-600' : 'from-gray-500 to-gray-600' }} rounded-lg p-4 text-white">
                        <div class="text-sm opacity-80">Alertas Totales</div>
                        <div class="text-2xl font-bold">{{ $totalAlertas }}</div>
                    </div>
                </div>

                <!-- Search -->
                <div class="mb-6">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar empresa..."
                        class="block w-full md:w-96 rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"
                    >
                </div>

                <!-- Companies Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($clientsWithConciliation as $item)
                        @php
                            $client = $item['client'];
                            $hasAlerts = $item['alertas'] > 0;
                        @endphp
                        <div class="bg-white border {{ $hasAlerts ? 'border-red-300 bg-red-50' : 'border-gray-200' }} rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <div class="p-5">
                                <!-- Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $client->company }}</h3>
                                        @if($client->fantasy_name)
                                            <p class="text-sm text-gray-500">{{ $client->fantasy_name }}</p>
                                        @endif
                                    </div>
                                    @if($hasAlerts)
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                            {{ $item['alertas'] }} alertas
                                        </span>
                                    @endif
                                </div>

                                <!-- Stats -->
                                <div class="space-y-3 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Ventas totales</span>
                                        <span class="font-medium text-gray-900">${{ number_format($item['total_ventas'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Turnos</span>
                                        <span class="font-medium text-gray-900">{{ $item['total_turnos'] }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Periodo</span>
                                        <span class="text-gray-900">
                                            @if($item['fecha_min'] && $item['fecha_max'])
                                                {{ \Carbon\Carbon::parse($item['fecha_min'])->format('d/m/Y') }} -
                                                {{ \Carbon\Carbon::parse($item['fecha_max'])->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <!-- Conciliation Percentages -->
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-blue-50 rounded-lg p-2 text-center">
                                        <div class="text-xs text-blue-600">MP</div>
                                        <div class="text-lg font-bold {{ $item['mp_pct'] < 95 ? 'text-red-600' : 'text-blue-700' }}">
                                            {{ $item['mp_pct'] }}%
                                        </div>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-2 text-center">
                                        <div class="text-xs text-green-600">Getnet</div>
                                        <div class="text-lg font-bold {{ $item['getnet_pct'] < 95 ? 'text-red-600' : 'text-green-700' }}">
                                            {{ $item['getnet_pct'] }}%
                                        </div>
                                    </div>
                                </div>

                                <!-- Action -->
                                @if($item['latest_execution'])
                                    <a href="{{ route('programmer.conciliacion.show', $item['latest_execution']) }}"
                                       class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                                        Ver Conciliacion
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 text-gray-500">
                            <i class="fas fa-building text-4xl mb-3"></i>
                            <p>No hay empresas con datos de conciliacion</p>
                            <a href="{{ route('programmer.workflows.upload') }}" class="mt-3 inline-block text-purple-600 hover:text-purple-800">
                                Ejecutar una conciliacion
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
