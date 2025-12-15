@php
    $user = auth()->user();
@endphp

<nav x-data="{ open: false }" class="bg-primary border-b border-primary-dark shadow-md sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}">
                        <div class="bg-white p-1 rounded-md shadow">
                            <img src="{{ asset('img/logo.png') }}" alt="Admin Ops" class="block h-9 w-auto">
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ms-10 sm:flex items-center space-x-4">
                    <!-- Admin Dashboard -->
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                        class="flex items-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        <span>Panel Principal</span>
                    </x-nav-link>

                    <!-- Usuarios y Equipos -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-200 hover:text-white transition"
                            :class="{'text-white': {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.teams.*') ? 'true' : 'false' }}}">
                            <i class="fas fa-users-cog mr-2"></i>
                            <span>Usuarios</span>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <x-dropdown-link :href="route('admin.users.index')"
                                    :active="request()->routeIs('admin.users.*')">
                                    <i class="fas fa-user-plus mr-2"></i> Gestión de Usuarios
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.users.create')"
                                    :active="request()->routeIs('admin.users.create')">
                                    <i class="fas fa-user-plus mr-2"></i> Crear Usuario
                                </x-dropdown-link>
                            </div>
                        </div>
                    </div>

                    <!-- Sistema -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-200 hover:text-white transition"
                            :class="{'text-white': {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.maintenance') || request()->routeIs('admin.logs') ? 'true' : 'false' }}}">
                            <i class="fas fa-cogs mr-2"></i>
                            <span>Sistema</span>
                            <i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute z-50 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <x-dropdown-link :href="route('admin.settings.email')"
                                    :active="request()->routeIs('admin.settings.email')">
                                    <i class="fas fa-envelope-open-text mr-2"></i> Config. Email
                                </x-dropdown-link>
                                <div class="border-t border-gray-100"></div>
                                <x-dropdown-link :href="route('admin.maintenance')"
                                    :active="request()->routeIs('admin.maintenance')">
                                    <i class="fas fa-tools mr-2"></i> Mantenimiento
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                                    <i class="fas fa-clipboard-list mr-2"></i> Logs
                                </x-dropdown-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-200 hover:text-white bg-primary-dark/40 hover:bg-primary-dark focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                <i class="fas fa-user-shield text-xl mr-2"></i>
                                <span class="mr-1">{{ $user->name }}</span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-400">
                            {{ $user->email }}
                        </div>
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                            <i class="fas fa-user-edit w-4 mr-2"></i> Mi Perfil
                        </x-dropdown-link>
                        <div class="border-t border-gray-200"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center text-red-600 hover:text-red-700 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-4 mr-2"></i> Cerrar Sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
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
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                <i class="fas fa-tachometer-alt w-5 mr-2"></i> Panel Principal
            </x-responsive-nav-link>

            <!-- Usuarios Section -->
            <div class="border-l-4 border-transparent">
                <div class="text-gray-300 px-4 py-2 text-xs font-semibold">USUARIOS</div>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    <i class="fas fa-users-cog w-5 mr-2"></i> Gestión de Usuarios
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.create')"
                    :active="request()->routeIs('admin.users.create')">
                    <i class="fas fa-user-plus w-5 mr-2"></i> Crear Usuario
                </x-responsive-nav-link>
            </div>

            <!-- Sistema Section -->
            <div class="border-l-4 border-transparent">
                <div class="text-gray-300 px-4 py-2 text-xs font-semibold">SISTEMA</div>
                <x-responsive-nav-link :href="route('admin.settings.email')"
                    :active="request()->routeIs('admin.settings.email')">
                    <i class="fas fa-envelope-open-text w-5 mr-2"></i> Config. Email
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.maintenance')"
                    :active="request()->routeIs('admin.maintenance')">
                    <i class="fas fa-tools w-5 mr-2"></i> Mantenimiento
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.logs')" :active="request()->routeIs('admin.logs')">
                    <i class="fas fa-clipboard-list w-5 mr-2"></i> Logs
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-primary-dark">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ $user->name }}</div>
                <div class="font-medium text-sm text-gray-300">{{ $user->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user-edit w-5 mr-2"></i> Mi Perfil
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-400">
                        <i class="fas fa-sign-out-alt w-5 mr-2"></i> Cerrar Sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>