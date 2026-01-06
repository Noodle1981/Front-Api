<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
            <i class="fas fa-user-shield mr-2 text-brand-dark"></i> Centro de Comando - Programador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Executive Summary Panel --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @if($stats['system_health'] === 'healthy')
                            <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-2xl text-green-600"></i>
                            </div>
                        @elseif($stats['system_health'] === 'warning')
                            <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center mr-4">
                                <i class="fas fa-exclamation-triangle text-2xl text-yellow-600"></i>
                            </div>
                        @else
                            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
                                <i class="fas fa-times-circle text-2xl text-red-600"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Estado del Sistema</h3>
                            <p class="text-sm text-gray-600">
                                Tasa de fallo: {{ $stats['error_rate'] }}% | 
                                Tendencia: 
                                @if($stats['trend'] === 'improving')
                                    <span class="text-green-600"><i class="fas fa-arrow-down"></i> Mejorando</span>
                                @else
                                    <span class="text-red-600"><i class="fas fa-arrow-up"></i> Requiere atención</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">{{ $pendingRequests->count() }}</div>
                        <div class="text-xs text-gray-500 uppercase">Pedidos de Workflows</div>
                    </div>
                </div>
            </div>

            {{-- Workflow Requests Panel --}}
            @if($pendingRequests->isNotEmpty())
                <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg p-6 border border-white/20">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-envelope-open-text mr-2 text-brand-dark"></i> Pedidos de Workflows Pendientes
                    </h3>
                    <div class="space-y-3">
                        @foreach($pendingRequests as $request)
                            <div class="flex items-start p-3 rounded-lg border-l-4 bg-blue-50 border-blue-500">
                                <i class="fas fa-file-signature text-xl mr-3 mt-1 text-blue-600"></i>
                                <div class="flex-1">
                                    <div class="flex justify-between">
                                        <p class="font-semibold text-gray-900">{{ $request->title }} ({{ ucfirst($request->workflow_type) }})</p>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full 
                                            {{ $request->priority === 'high' ? 'bg-red-100 text-red-800' : ($request->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ strtoupper($request->priority) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-1">Solicitado por: <strong>{{ $request->user->name }}</strong> para <strong>{{ $request->client->company }}</strong></p>
                                    <p class="text-sm text-gray-700 mt-2">{{ Str::limit($request->description, 100) }}</p>
                                </div>
                                <div class="ml-4 flex flex-col space-y-2">
                                    <form action="{{ route('programmer.workflows.requests.accept', $request) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-3 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition">ACEPTAR</button>
                                    </form>
                                    <a href="{{ route('programmer.workflows.requests') }}" class="px-3 py-1 bg-white text-gray-700 text-xs font-bold rounded border border-gray-300 hover:bg-gray-50 transition text-center">VER</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-50/70 to-blue-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-blue-200/50 hover:from-blue-50/90 hover:to-blue-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-200 text-blue-700 mr-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-700 uppercase">Operadores</p>
                            <p class="text-3xl font-bold text-blue-900">{{ $stats['total_users'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-indigo-50/70 to-indigo-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-indigo-200/50 hover:from-indigo-50/90 hover:to-indigo-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-200 text-indigo-700 mr-4">
                            <i class="fas fa-building text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-indigo-700 uppercase">Clientes</p>
                            <p class="text-3xl font-bold text-indigo-900">{{ $stats['total_clients'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-50/70 to-red-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-red-200/50 hover:from-red-50/90 hover:to-red-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-200 text-red-700 mr-4">
                            <i class="fas fa-file-pdf text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-red-700 uppercase">Informes PDF</p>
                            <p class="text-3xl font-bold text-red-900">{{ $stats['pdf_reports'] }}</p>
                        </div>
                    </div>
                </div>
                 <div class="bg-gradient-to-br from-green-50/70 to-green-100/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg p-6 border border-green-200/50 hover:from-green-50/90 hover:to-green-100/90 transition-all">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-200 text-green-700 mr-4">
                            <i class="fas fa-paper-plane text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-green-700 uppercase">Workflows Enviados</p>
                            <p class="text-3xl font-bold text-green-900">{{ $stats['workflows_sent'] }}</p>
                        </div>
                    </div>
                </div>
            </div>



            {{-- Recent Workflows Widget --}}
            <div class="bg-white/70 backdrop-blur-md shadow-lg rounded-lg border border-white/20">
                <div class="p-6 border-b border-gray-200/50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-stream mr-2 text-brand-dark"></i> Workflows Recientes
                    </h3>
                    <div class="flex space-x-2">
                        <a href="{{ route('programmer.workflows.upload') }}" class="px-3 py-1 bg-green-600 text-white text-xs font-bold rounded hover:bg-green-700 transition">
                            <i class="fas fa-plus mr-1"></i> NUEVO
                        </a>
                        <a href="{{ route('programmer.workflows.history') }}" class="px-3 py-1 bg-brand-dark text-white text-xs font-bold rounded hover:bg-opacity-90 transition">
                            VER TODO
                        </a>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Workflow</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentBatches as $batch)
                                    <tr class="hover:bg-white/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ $batch->batch_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $batch->workflowType->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $batch->client->company ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-0.5 text-xs font-bold rounded-full 
                                                {{ $batch->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $batch->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $batch->status === 'validated' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $batch->status === 'executing' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $batch->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ strtoupper($batch->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('programmer.workflows.batch.show', $batch) }}" class="text-brand-dark hover:underline font-bold">Ver</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">
                                            No hay workflows recientes cargados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Users Table with Enhanced Metrics --}}
            <div class="bg-white/70 backdrop-blur-md overflow-hidden shadow-lg sm:rounded-lg border border-white/20">
                <div class="p-6 bg-white/50 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800">Operadores - Vista Detallada</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Clientes Asignados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actividad</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/30 divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-white/60 transition {{ $user->has_alerts ? 'bg-yellow-50/50' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <span class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center text-gray-700 font-bold text-lg">
                                                    {{ substr($user->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                            @if($user->has_alerts)
                                                <i class="fas fa-exclamation-circle text-yellow-500 ml-2" title="Requiere atención"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                            {{ $user->clients_count }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($user->last_activity)
                                            <span class="{{ $user->days_inactive > 7 ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                                {{ $user->last_activity->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 italic">Sin actividad registrada</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <a href="{{ route('programmer.api-dashboard', ['user_filter' => $user->id]) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition" title="Ver Dashboard API">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <a href="{{ route('programmer.clients.index', ['user_filter' => $user->id]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 transition" title="Ver Clientes">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    

</x-app-layout>
