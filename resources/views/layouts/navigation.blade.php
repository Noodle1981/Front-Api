<nav x-data="{ open: false }" class="bg-background border-b border-white/20 shadow-md sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <div class="bg-background backdrop-blur-sm rounded-lg p-2 border-2 border-white/20 shadow-lg">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="block h-10 w-auto drop-shadow-lg">
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex items-center space-x-4">
                    <!-- Dashboard -->
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                        class="flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        <span>Dashboard</span>
                    </x-nav-link>

                    <!-- Clientes -->
                    <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')"
                        class="flex items-center">
                        <i class="fas fa-building mr-2"></i>
                        <span>Clientes</span>
                    </x-nav-link>

                    <!-- Admin Section -->
                    @if(auth()->user()->is_admin)
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center px-3 py-2 text-sm font-medium text-gray-200 hover:text-white transition"
                                :class="{'text-white': {{ request()->routeIs('admin.*') ? 'true' : 'false' }}}">
                                <i class="fas fa-shield-alt mr-2"></i>
                                <span>Administración</span>
                                <i class="fas fa-chevron-down ml-2 text-xs"></i>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                class="absolute z-50 mt-2 w-56 rounded-2xl shadow-2xl bg-white/10 backdrop-blur-xl border border-white/20">
                                <div class="py-1 text-white">
                                    <x-dropdown-link :href="route('admin.dashboard')"
                                        :active="request()->routeIs('admin.dashboard')">
                                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard Global
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.users.index')"
                                        :active="request()->routeIs('admin.users.*')">
                                        <i class="fas fa-users-cog mr-2"></i> Gestión de Usuarios
                                    </x-dropdown-link>

                                    <div class="border-t border-gray-100"></div>

                                    <x-dropdown-link :href="route('admin.settings.email')"
                                        :active="request()->routeIs('admin.settings.*')">
                                        <i class="fas fa-cogs mr-2"></i> Configuración Sistema
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.maintenance')"
                                        :active="request()->routeIs('admin.maintenance')">
                                        <i class="fas fa-tools mr-2"></i> Mantenimiento
                                    </x-dropdown-link>

                                    <x-dropdown-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                                        <i class="fas fa-clipboard-list mr-2"></i> Logs del Sistema
                                    </x-dropdown-link>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="inline-flex items-center px-3 py-2 border border-white/20 text-sm leading-4 font-medium rounded-md text-gray-200 hover:text-white bg-white/10 backdrop-blur-xl focus:outline-none transition ease-in-out duration-150">
                        <div class="flex items-center">
                            <i class="fas fa-user-circle text-xl mr-2"></i>
                            <span class="mr-1">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-56 rounded-2xl shadow-2xl bg-white/10 backdrop-blur-xl border border-white/20 z-50">
                        <div class="px-4 py-2 text-xs text-gray-200">
                            {{ Auth::user()->email }}
                        </div>
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                            <i class="fas fa-user-edit w-4 mr-2"></i> Mi Perfil
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('settings.index')" class="flex items-center">
                            <i class="fas fa-cog w-4 mr-2"></i> Configuración
                        </x-dropdown-link>
                        <div class="border-t border-white/20"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center text-red-400 hover:text-red-500 hover:bg-red-900/20">
                                <i class="fas fa-sign-out-alt w-4 mr-2"></i> Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-primary-dark focus:outline-none focus:bg-primary-dark/50 transition duration-150 ease-in-out">
                    <span class="sr-only">Abrir menú principal</span>
                    <i class="fas fa-bars h-6 w-6" x-show="!open"></i>
                    <i class="fas fa-times h-6 w-6" x-show="open"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-primary">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Dashboard -->
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                class="flex items-center">
                <i class="fas fa-chart-line w-5 mr-2"></i>
                <span>Dashboard</span>
            </x-responsive-nav-link>

            <!-- Clientes -->
            <x-responsive-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')"
                class="flex items-center">
                <i class="fas fa-building w-5 mr-2"></i>
                <span>Clientes</span>
            </x-responsive-nav-link>

            <!-- Admin Panel -->
            @if(auth()->user()->is_admin)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    <i class="fas fa-shield-alt w-5 mr-2"></i> Panel Admin
                </x-responsive-nav-link>
            @endif
        </div>
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-primary-dark">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Mi Perfil</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('settings.index')">Configuración</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">Cerrar
                        Sesión</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>