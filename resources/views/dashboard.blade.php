<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings text-3xl font-extrabold text-primary-light tracking-tight drop-shadow mb-2">
            Dashboard
        </h2>
    </x-slot>

    <div class="bg-background min-h-screen w-full p-4 md:p-6 space-y-6">

        {{-- Welcome Section --}}
        <div
            class="bg-white/10 backdrop-blur-md rounded-xl p-8 border border-white/20 shadow-xl text-center md:text-left">
            <h3 class="text-2xl font-bold text-white mb-2">¡Hola, {{ Auth::user()->name }}!</h3>
            <p class="text-white/80">Bienvenido al Portal de Clientes de Grupo Xamanen.</p>
        </div>

        {{-- KPIs Quick View --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-kpi-card title="Mis Clientes" :value="$clientCount" icon="fas fa-building"
                class="bg-primary-dark text-white shadow-xl" />

            {{-- Placeholder for future KPIs or removed if not needed --}}
            {{--
            <x-kpi-card title="Servicios Activos" :value="'0'" icon="fas fa-cogs"
                class="bg-primary-dark text-white shadow-xl" />
            <x-kpi-card title="Documentos" :value="'0'" icon="fas fa-file-alt"
                class="bg-primary-dark text-white shadow-xl" />
            --}}
        </div>

    </div>

</x-app-layout>