@extends('layouts.admin')

@section('content')
    <div class="space-y-6">

        <!-- 1. Header Premium (ROI & Business Focus) -->
        <div class="bg-gradient-to-r from-[#0C263B] to-brand-dark rounded-2xl shadow-xl p-8 text-white overflow-hidden relative border-b-4 border-brand-accent/30">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black mb-1 flex items-center">
                        <i class="fas fa-chart-pie mr-3 text-brand-accent"></i> CONTROL ESTRATÉGICO
                    </h2>
                    <p class="text-blue-100 opacity-80 font-medium">Análisis de ROI, impacto operativo y calidad de procesos.</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Filtros Glassmorphism -->
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
                </div>
            </div>
            <!-- Decoración -->
            <i class="fas fa-balance-scale absolute right-[-20px] top-[-10px] text-white/5 text-9xl rotate-12"></i>
        </div>

        <!-- 2. Tarjeta de Impacto Operativo (ROI Real) -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 relative overflow-hidden group">
            <div class="relative z-10">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-black text-gray-800 uppercase tracking-tight">Beneficios del Sistema</h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Impacto acumulado en el período seleccionado</p>
                    </div>
                    <div class="bg-brand-dark/5 p-3 rounded-2xl text-brand-dark group-hover:bg-brand-dark group-hover:text-white transition-all duration-300">
                        <i class="fas fa-rocket text-2xl"></i>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Horas Ganadas -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tiiempo Ahorrado</span>
                            <span class="bg-emerald-100 text-emerald-700 text-[9px] font-black px-2 py-0.5 rounded-full">+{{ $stats['efficiency_gain'] }}% EFICIENCIA</span>
                        </div>
                        <div class="flex items-baseline">
                            <span class="text-5xl font-black text-brand-dark tracking-tighter">{{ $stats['hours_saved'] }}</span>
                            <span class="text-xl font-bold text-gray-400 ml-2">Horas</span>
                        </div>
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $stats['efficiency_gain'] }}%"></div>
                        </div>
                        <p class="text-[10px] text-gray-400 italic">Basado en {{ $stats['totalExecutions'] }} workflows procesados automaticamente.</p>
                    </div>

                    <!-- Costo Evitado -->
                    <div class="space-y-4 border-x border-gray-100 px-8">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ahorro Estimado</span>
                            <i class="fas fa-coins text-amber-400"></i>
                        </div>
                        <div class="flex items-baseline">
                            <span class="text-5xl font-black text-brand-dark tracking-tighter">${{ number_format($stats['cost_saved'], 0, ',', '.') }}</span>
                        </div>
                        <p class="text-[10px] text-gray-400 leading-relaxed">Reducción de costos por automatización de taras de conciliación manual.</p>
                    </div>

                    <!-- Optimización -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Carga Operativa</span>
                            <span class="text-blue-500"><i class="fas fa-layer-group"></i></span>
                        </div>
                        <div class="flex items-baseline">
                            <span class="text-5xl font-black text-brand-dark tracking-tighter">{{ round($stats['hours_saved'] / 8, 1) }}</span>
                            <span class="text-xl font-bold text-gray-400 ml-2">Días</span>
                        </div>
                        <p class="text-[10px] text-gray-400 italic">Equivalente a jornadas de trabajo completas liberadas para tareas estratégicas.</p>
                    </div>
                </div>
            </div>
            <!-- Decoración sutil -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-brand-dark/[0.02] rounded-full blur-3xl group-hover:bg-brand-dark/[0.05] transition-all"></div>
        </div>

        <!-- 3. Métricas de Negocio (Cards) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 border-l-4 border-emerald-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 mr-4">
                        <i class="fas fa-dollar-sign text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Ventas Totales</div>
                        <div class="text-2xl font-black text-gray-900">${{ number_format($stats['totalVentas'] ?? 0, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600 mr-4">
                        <i class="fas fa-ticket-alt text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Total Tickets</div>
                        <div class="text-2xl font-black text-gray-900">{{ number_format($stats['totalTickets'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 border-l-4 border-amber-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-amber-50 text-amber-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Comensales</div>
                        <div class="text-2xl font-black text-gray-900">{{ number_format($stats['totalComensales'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 rounded-xl bg-purple-50 text-purple-600 mr-4">
                        <i class="fas fa-check-double text-xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Qualidad Media</div>
                        <div class="text-2xl font-black text-gray-900">{{ $conciliacion['promedio'] ?? 0 }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Panel de Calidad de Conciliación -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-200 flex items-center justify-between text-sm font-black uppercase tracking-widest text-gray-600">
                <span><i class="fas fa-percentage mr-3 text-brand-dark"></i> Eficiencia por Pasarela de Pago</span>
            </div>
            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Mercado Pago -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-700 uppercase tracking-widest transition-colors hover:text-blue-600 cursor-default">Mercado Pago</span>
                        <span class="text-sm font-black text-blue-600">{{ $conciliacion['mercado_pago'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 relative shadow-inner">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-3 rounded-full transition-all duration-1000 shadow-lg" style="width:{{ $conciliacion['mercado_pago'] ?? 0 }}%"></div>
                    </div>
                </div>

                <!-- Getnet -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-700 uppercase tracking-widest transition-colors hover:text-red-600 cursor-default">Getnet</span>
                        <span class="text-sm font-black text-red-600">{{ $conciliacion['getnet'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 relative shadow-inner">
                        <div class="bg-gradient-to-r from-red-400 to-red-600 h-3 rounded-full transition-all duration-1000 shadow-lg" style="width:{{ $conciliacion['getnet'] ?? 0 }}%"></div>
                    </div>
                </div>

                <!-- Efectivo -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-gray-700 uppercase tracking-widest transition-colors hover:text-emerald-600 cursor-default">Contado / Efvo</span>
                        <span class="text-sm font-black text-emerald-600">{{ $conciliacion['efectivo'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 relative shadow-inner">
                        <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-3 rounded-full transition-all duration-1000 shadow-lg" style="width:{{ $conciliacion['efectivo'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Distribución y Operaciones -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Workflows por Programador -->
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
                <div class="bg-gray-50/50 px-8 py-4 border-b border-gray-100 text-[11px] font-black uppercase tracking-widest text-gray-500">
                    Productividad del Equipo Técnico
                </div>
                <div class="p-0">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-gray-400 font-bold uppercase tracking-tighter border-b">
                            <tr>
                                <th class="px-8 py-3">Responsable</th>
                                <th class="px-8 py-3 text-right">Workflows Completados</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium">
                            @forelse($workflowsByProgrammer as $programmer)
                                <tr class="hover:bg-brand-dark/[0.02] transition-colors group">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-xl bg-brand-dark/10 flex items-center justify-center text-brand-dark mr-4 text-[10px] font-black group-hover:bg-brand-dark group-hover:text-white transition-all">
                                                {{ substr($programmer->name, 0, 2) }}
                                            </div>
                                            <span class="text-gray-800 font-bold">{{ $programmer->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <span class="inline-flex items-center px-4 py-1 rounded-full text-[10px] font-black bg-purple-100 text-purple-700 border border-purple-200">
                                            {{ $programmer->workflows_count }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-8 py-12 text-center text-gray-400 uppercase text-[10px] font-black tracking-widest opacity-40">
                                        Sin datos en el período
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actividad General -->
            <div class="bg-white shadow-sm rounded-2xl p-8 border border-gray-100">
                <h3 class="font-black text-gray-800 text-[11px] uppercase tracking-[0.2em] mb-8 flex items-center">
                    <i class="fas fa-info-circle mr-3 text-brand-dark opacity-50"></i> Meta-Información del Sistema
                </h3>
                <div class="space-y-8">
                    <div class="flex items-center justify-between group">
                        <span class="text-[11px] text-gray-400 uppercase font-black tracking-widest group-hover:text-brand-dark transition-colors">Clientes Totales</span>
                        <span class="text-2xl font-black text-gray-900">{{ $stats['totalClients'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between group">
                        <span class="text-[11px] text-gray-400 uppercase font-black tracking-widest group-hover:text-brand-dark transition-colors">Algoritmos Activos</span>
                        <span class="text-2xl font-black text-gray-900">{{ $stats['workflowTypes'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between group">
                        <span class="text-[11px] text-gray-400 uppercase font-black tracking-widest group-hover:text-brand-dark transition-colors">Performance Motor</span>
                        <div class="flex items-baseline">
                            <span class="text-2xl font-black text-brand-dark">{{ $stats['avgExecutionTime'] ?? 0 }}</span>
                            <span class="text-xs font-bold text-gray-300 ml-1">seg</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection