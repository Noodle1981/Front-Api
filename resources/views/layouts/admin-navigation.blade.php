@php
    $user = auth()->user();
@endphp

<nav class="h-full flex flex-col justify-between py-6 px-4 space-y-6">
    <!-- Logo & Links -->
    <div class="space-y-6">
        <!-- Logo -->
        <div class="flex items-center justify-center mb-6">
            <a href="{{ route('admin.dashboard') }}">
                <div class="p-1 rounded-md">
                    <img src="{{ asset('img/logo.png') }}" alt="Admin Ops" class="block h-12 w-auto">
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex flex-col space-y-2">

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-2 mb-1">General</div>

            <!-- Panel Principal -->
            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                <i class="fas fa-tachometer-alt w-6 text-center mr-3"></i>
                <span class="font-medium">Dashboard</span>
            </x-nav-link>

            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Gestión</div>

            <!-- Usuarios -->
            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')"
                class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                <i class="fas fa-users-cog w-6 text-center mr-3"></i>
                <span class="font-medium">Usuarios</span>
            </x-nav-link>

            <!-- Servicios API -->
            <x-nav-link :href="route('admin.api-services.index')" :active="request()->routeIs('admin.api-services.*')"
                class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                <i class="fas fa-plug w-6 text-center mr-3"></i>
                <span class="font-medium">Servicios API</span>
            </x-nav-link>

            <!-- Sistema Section (Expanded) -->
            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Sistema</div>

            <x-nav-link :href="route('admin.settings.email')" :active="request()->routeIs('admin.settings.email')"
                class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                <i class="fas fa-envelope-open-text w-6 text-center mr-3"></i>
                <span class="font-medium">Email</span>
            </x-nav-link>

            <x-nav-link :href="route('admin.maintenance')" :active="request()->routeIs('admin.maintenance')"
                class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                <i class="fas fa-tools w-6 text-center mr-3"></i>
                <span class="font-medium">Mantenimiento</span>
            </x-nav-link>

            <x-nav-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')"
                class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                <i class="fas fa-clipboard-list w-6 text-center mr-3"></i>
                <span class="font-medium">Logs</span>
            </x-nav-link>

        </div>
    </div>

    <!-- User Profile & Logout -->
    <div class="border-t border-white/10 pt-6">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="w-full flex items-center px-2 py-2 text-sm font-medium text-gray-200 hover:text-white rounded-md hover:bg-white/5 transition focus:outline-none">
                <div class="flex items-center flex-1 min-w-0">
                    <div
                        class="w-8 h-8 rounded-full bg-brand-accent/20 flex items-center justify-center text-brand-accent mr-3">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="flex flex-col items-start truncate">
                        <span class="truncate">{{ $user->name }}</span>
                        <span class="text-xs text-gray-400 truncate">Administrador</span>
                    </div>
                </div>
                <i class="fas fa-chevron-up text-xs ml-2"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false"
                class="absolute bottom-full left-0 w-full mb-2 rounded-lg shadow-lg bg-[#0C263B] border border-white/10 overflow-hidden z-50">
                <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 border-b border-white/5">
                    <i class="fas fa-user-edit mr-2"></i> Mi Perfil
                </a>

                {{-- Toggle to User View (Optional, nice to have for Admins) --}}
                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-3 text-sm text-green-400 hover:text-green-300 hover:bg-white/5 border-b border-white/5">
                    <i class="fas fa-exchange-alt mr-2"></i> Vista Usuario
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-3 text-sm text-red-400 hover:text-red-300 hover:bg-red-900/10 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>