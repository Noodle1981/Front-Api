@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Logs del Sistema</h2>
        <div class="text-sm text-gray-600">
            Mostrando {{ $logs->count() }} de {{ $logs->total() }} registros
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            @if($logs->count() > 0)
                <div class="space-y-3">
                    @foreach($logs as $log)
                        @php
                            $borderColor = match($log->type) {
                                'deal_created' => 'border-green-500',
                                'deal_won' => 'border-blue-500',
                                'deal_lost' => 'border-red-500',
                                'lead_created' => 'border-yellow-500',
                                'lead_converted' => 'border-purple-500',
                                default => 'border-gray-400'
                            };
                            
                            $iconColor = match($log->type) {
                                'deal_created' => 'text-green-600',
                                'deal_won' => 'text-blue-600',
                                'deal_lost' => 'text-red-600',
                                'lead_created' => 'text-yellow-600',
                                'lead_converted' => 'text-purple-600',
                                default => 'text-gray-600'
                            };
                            
                            $icon = match($log->type) {
                                'deal_created' => 'fa-handshake',
                                'deal_won' => 'fa-trophy',
                                'deal_lost' => 'fa-times-circle',
                                'lead_created' => 'fa-user-plus',
                                'lead_converted' => 'fa-exchange-alt',
                                default => 'fa-circle'
                            };
                        @endphp
                        
                        <div class="border-l-4 {{ $borderColor }} bg-gray-50 p-4 rounded-r-lg hover:bg-gray-100 transition">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center">
                                        <i class="fas {{ $icon }} {{ $iconColor }}"></i>
                                    </div>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $log->description }}
                                            </p>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-user text-xs mr-1"></i>
                                                {{ $log->user->name ?? 'Sistema' }}
                                            </p>
                                        </div>
                                        
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">
                                                {{ $log->created_at->diffForHumans() }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    @if($log->properties && is_array($log->properties))
                                        <div class="mt-2 pt-2 border-t border-gray-200">
                                            <details class="text-xs">
                                                <summary class="text-blue-600 hover:text-blue-800 cursor-pointer">
                                                    Ver detalles
                                                </summary>
                                                <div class="mt-2 bg-gray-800 text-gray-100 p-3 rounded text-xs overflow-x-auto">
                                                    <pre>{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </div>
                                            </details>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay registros de actividad disponibles</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection