@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800">Logs del Sistema</h2>
            <div class="text-sm text-gray-600">
                Mostrando las últimas entradas del archivo <code>laravel.log</code>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Fecha</th>
                            <th scope="col" class="px-6 py-3">Nivel</th>
                            <th scope="col" class="px-6 py-3">Mensaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            @php
                                $badgeColor = match ($log['level']) {
                                    'ERROR' => 'bg-red-100 text-red-800',
                                    'WARNING' => 'bg-yellow-100 text-yellow-800',
                                    'INFO' => 'bg-blue-100 text-blue-800',
                                    'DEBUG' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-xs">
                                    {{ $log['date'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="{{ $badgeColor }} text-xs font-medium mr-2 px-2.5 py-0.5 rounded">
                                        {{ $log['level'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-gray-600 break-all">
                                    {{ $log['raw'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                                    <p>El archivo de logs está limpio o vacío.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection