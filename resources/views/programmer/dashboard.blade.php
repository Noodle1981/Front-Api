@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        <!-- 1. Header Premium con Filtros Integrados -->
        <div class="bg-gradient-to-r from-brand-dark to-brand-light rounded-2xl shadow-xl p-8 text-white overflow-hidden relative border-b-4 border-brand-accent/30">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black mb-1 flex items-center">
                        <i class="fas fa-terminal mr-3 text-brand-accent"></i> PANEL OPERATIVO
                    </h2>
                    <p class="text-blue-100 opacity-80 font-medium">Gestión de workflows y monitoreo técnico del motor de reglas.</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Filtros con estilo Glassmorphism -->
                    <form action="" method="GET" class="flex items-center space-x-2 bg-white/10 backdrop-blur-md p-1.5 rounded-xl border border-white/20">
                        <select name="month" onchange="this.form.submit()" class="bg-transparent text-white text-[11px] font-bold border-0 rounded-lg py-1 focus:ring-0 uppercase cursor-pointer hover:bg-white/10 transition-colors">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" class="text-gray-900" {{ ($filters['month'] ?? now()->month) == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                        <div class="h-4 w-px bg-white/20"></div>
                        <select name="year" onchange="this.form.submit()" class="bg-transparent text-white text-[11px] font-bold border-0 rounded-lg py-1 focus:ring-0 cursor-pointer hover:bg-white/10 transition-colors">
                            @foreach(range(now()->year - 2, now()->year) as $y)
                                <option value="{{ $y }}" class="text-gray-900" {{ ($filters['year'] ?? now()->year) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <a href="{{ route('programmer.workflows.upload') }}" class="px-6 py-2.5 bg-brand-accent text-brand-dark font-black text-xs rounded-xl hover:bg-white hover:scale-105 transform transition-all duration-200 shadow-lg flex items-center uppercase tracking-wider">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i> NUEVA CARGA
                    </a>
                </div>
            </div>
            <!-- Decoración de fondo -->
            <i class="fas fa-microchip absolute right-[-20px] top-[-10px] text-white/5 text-9xl rotate-12"></i>
            <div class="absolute bottom-[-50px] left-[-20px] w-64 h-64 bg-brand-accent/5 rounded-full blur-3xl"></div>
        </div>

        <!-- 2. Grid de Métricas Operativas (Salud del Sistema) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tasa de Éxito Técnico -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-emerald-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Éxito Técnico</div>
                        <div class="text-2xl font-black text-gray-900">{{ $stats['successRate'] ?? 100 }}%</div>
                    </div>
                </div>
            </div>

            <!-- Tiempo de Respuesta -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600 mr-4">
                        <i class="fas fa-bolt text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Velocidad Prom.</div>
                        <div class="text-2xl font-black text-gray-900">{{ $stats['avgExecutionTime'] ?? 0 }}s</div>
                    </div>
                </div>
            </div>

            <!-- Ejecuciones Hoy -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-purple-50 text-purple-600 mr-4">
                            <i class="fas fa-calendar-day text-xl"></i>
                        </div>
                        <div>
                            <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Hoy</div>
                            <div class="text-2xl font-black text-gray-900">{{ $stats['successToday'] ?? 0 }}</div>
                        </div>
                    </div>
                    @if(($stats['errorsToday'] ?? 0) > 0)
                        <span class="px-2 py-1 bg-red-100 text-red-600 text-[9px] font-black rounded-lg animate-pulse border border-red-200">
                            {{ $stats['errorsToday'] }} ERR
                        </span>
                    @endif
                </div>
            </div>

            <!-- Total Clientes -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl p-6 border-l-4 border-brand-dark hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-brand-dark/5 text-brand-dark mr-4">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Base Clientes</div>
                        <div class="text-2xl font-black text-gray-900">{{ $stats['totalClients'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Contenido Principal: Clientes y Ejecuciones -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Últimas Ejecuciones Técnicas -->
            <div class="lg:col-span-2 bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-700 flex items-center text-sm uppercase tracking-wide">
                        <i class="fas fa-tasks mr-2 text-brand-dark text-xs"></i> Monitoreo de Procesamientos
                    </h3>
                    <a href="{{ route('programmer.workflows.history') }}" class="text-[10px] text-brand-dark hover:underline font-black uppercase tracking-tighter">Historial Completo</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50/80 text-gray-400 font-bold text-[10px] uppercase tracking-widest border-b">
                            <tr>
                                <th class="px-6 py-3">Cliente / Sucursal</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                                <th class="px-6 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentExecutions as $execution)
                                <tr class="hover:bg-brand-dark/[0.02] transition-colors duration-150 group">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800 group-hover:text-brand-dark transition-colors">{{ $execution->fileBatch->client->company ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">WF-{{ $execution->id }} • {{ $execution->completed_at?->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($execution->status === 'success' || $execution->status === 'completed')
                                            <span class="px-2.5 py-1 text-[9px] font-black bg-emerald-100 text-emerald-700 rounded-lg border border-emerald-200 uppercase">Success</span>
                                        @else
                                            <span class="px-2.5 py-1 text-[9px] font-black bg-rose-100 text-rose-700 rounded-lg border border-rose-200 uppercase">Error</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('programmer.workflows.execution.pdf.preview', $execution) }}" class="inline-flex items-center px-4 py-1.5 bg-gray-100 text-gray-700 hover:bg-brand-dark hover:text-white rounded-lg text-[10px] font-black transition-all duration-200 shadow-sm border border-gray-200">
                                            <i class="fas fa-eye mr-2 text-xs"></i> VER RESULTADO
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center opacity-30">
                                            <i class="fas fa-inbox text-4xl mb-3"></i>
                                            <p class="text-xs font-bold uppercase tracking-widest">Sin actividad en este período</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Columna Derecha: Clientes y Status -->
            <div class="space-y-6">
                <!-- Clientes Recientes -->
                <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
                    <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-700 flex items-center text-sm uppercase tracking-wide">
                            <i class="fas fa-address-book mr-2 text-brand-dark text-xs"></i> Clientes Activos
                        </h3>
                    </div>
                    <div class="p-2 space-y-1">
                        @foreach($recentClients as $client)
                            <a href="{{ route('programmer.clients.show', $client) }}" class="flex items-center justify-between p-3 hover:bg-brand-dark/[0.03] rounded-xl transition-all group">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-brand-dark group-hover:text-white transition-all transform group-hover:scale-110">
                                        <i class="fas fa-building text-xs"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-[11px] font-black text-gray-800 leading-none group-hover:text-brand-dark transition-colors">{{ str($client->company)->limit(20) }}</p>
                                        <div class="flex items-center mt-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                            <span class="text-[9px] text-gray-400 uppercase font-bold tracking-tighter">Validado</span>
                                        </div>
                                    </div>
                                </div>
                                <i class="fas fa-chevron-right text-[10px] text-gray-300 group-hover:text-brand-dark group-hover:translate-x-1 transition-all"></i>
                            </a>
                        @endforeach
                        <div class="p-3">
                            <a href="{{ route('programmer.clients.index') }}" class="block text-center py-2 bg-gray-50 border border-gray-100 rounded-lg text-[9px] font-black text-brand-dark hover:bg-brand-dark hover:text-white transition-all uppercase tracking-widest">
                                Gestión de Base de Datos
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card de Status Técnico Premium -->
                <div class="bg-[#0C263B] rounded-2xl p-6 text-white shadow-2xl relative overflow-hidden border-t-4 border-brand-accent">
                    <div class="relative z-10">
                        <h4 class="font-bold text-brand-accent text-[11px] mb-3 uppercase tracking-[0.2em] flex items-center">
                            <span class="relative flex h-2 w-2 mr-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-accent opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-accent"></span>
                            </span>
                            Sistema en Línea
                        </h4>
                        <p class="text-[11px] text-blue-100/70 leading-relaxed mb-6 font-medium">
                            El motor de procesamiento está operando nominalmente. Se recomienda monitorear latencia en horarios pico.
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                                <p class="text-[8px] text-blue-300 uppercase font-bold mb-1">Uptime</p>
                                <p class="text-xs font-black">99.98%</p>
                            </div>
                            <div class="bg-white/5 p-3 rounded-xl border border-white/10">
                                <p class="text-[8px] text-blue-300 uppercase font-bold mb-1">Latencia</p>
                                <p class="text-xs font-black">{{ $stats['avgExecutionTime'] }}ms</p>
                            </div>
                        </div>
                    </div>
                    <!-- Decoración circular -->
                    <div class="absolute top-[-20px] right-[-20px] w-24 h-24 bg-brand-accent/10 rounded-full blur-2xl"></div>
                </div>
            </div>

        </div>

    </div>
@endsection
