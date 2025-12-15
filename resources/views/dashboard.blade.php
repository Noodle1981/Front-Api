<x-app-layout>
    <x-slot name="header">
        <h2 class="font-headings text-3xl font-extrabold text-primary-light tracking-tight drop-shadow mb-2">
            Dashboard
        </h2>
    </x-slot>

    <div class="bg-background min-h-screen w-full p-4 md:p-6 space-y-6">

        {{-- Welcome Section --}}
        <div
            class="bg-white/90 backdrop-blur-sm rounded-xl p-8 border border-white/20 shadow-xl text-center md:text-left text-gray-900">
            <h3 class="text-2xl font-bold text-brand-dark mb-2">¡Hola, {{ Auth::user()->name }}!</h3>
            <p class="text-gray-700">Bienvenido al Portal de Clientes de Grupo Xamanen.</p>
        </div>

        {{-- KPIs Quick View --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- KPI Card with Brand Color --}}
            <x-kpi-card title="Mis Clientes" :value="$clientCount" icon="fas fa-building"
                class="bg-brand-dark text-white shadow-xl" />

            {{-- Placeholder for future KPIs or removed if not needed --}}
        </div>

    </div>

</x-app-layout>