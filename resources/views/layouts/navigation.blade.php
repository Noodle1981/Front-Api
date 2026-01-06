<nav class="h-full flex flex-col justify-between py-6 px-4 space-y-6">
    <!-- Logo & Links -->
    <div class="space-y-6">
        <!-- Logo -->
        <div class="flex items-center justify-center mb-6">
            <a href="{{ route('dashboard') }}">
                <div class="p-2">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="block h-12 w-auto">
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex flex-col space-y-2">
            @role('Programador')
                {{-- Rutas de PROGRAMADOR (Ex Analista) --}}
                <x-nav-link :href="route('programmer.dashboard')" :active="request()->routeIs('programmer.dashboard')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-user-shield w-6 text-center mr-3"></i>
                    <span class="font-medium">Panel Programador</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.enterprise.index')" :active="request()->routeIs('programmer.enterprise.*')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-server w-6 text-center mr-3"></i>
                    <span class="font-medium">Enterprise</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.clients.index')" :active="request()->routeIs('programmer.clients.*')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-building w-6 text-center mr-3"></i>
                    <span class="font-medium">Clientes</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.api-dashboard')" :active="request()->routeIs('programmer.api-dashboard')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-tower-broadcast w-6 text-center mr-3"></i>
                    <span class="font-medium">Monitor APIs</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.workflows.requests')" :active="request()->routeIs('programmer.workflows.requests*')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200 text-aurora-cyan">
                    <i class="fas fa-envelope-open-text w-6 text-center mr-3"></i>
                    <span class="font-medium">Pedidos de Workflows</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.business-rules.index')" :active="request()->routeIs('programmer.business-rules.*')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-code-branch w-6 text-center mr-3"></i>
                    <span class="font-medium">Reglas de Negocio</span>
                </x-nav-link>
            @else
                {{-- Rutas de OPERADOR (User) --}}
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-chart-line w-6 text-center mr-3"></i>
                    <span class="font-medium">Dashboard</span>
                </x-nav-link>
                
                <x-nav-link :href="route('clients.index')" :active="request()->routeIs('clients.*')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-building w-6 text-center mr-3"></i>
                    <span class="font-medium">Clientes</span>
                </x-nav-link>

                <x-nav-link :href="route('operator.workflows.history')" :active="request()->routeIs('operator.workflows.history')" class="flex items-center space-x-2">
                    <i class="fas fa-history w-5"></i>
                    <span class="font-medium">Historial Workflows</span>
                </x-nav-link>

                <x-nav-link :href="route('operator.workflows.my-requests')" :active="request()->routeIs('operator.workflows.my-requests')" class="flex items-center space-x-2">
                    <i class="fas fa-clipboard-list w-5"></i>
                    <span class="font-medium">Mis Pedidos</span>
                </x-nav-link>

                <x-nav-link :href="route('operator.workflows.request')" :active="request()->routeIs('operator.workflows.request')" class="flex items-center space-x-2">
                    <i class="fas fa-paper-plane w-5"></i>
                    <span class="font-medium">Solicitar Workflow</span>
                </x-nav-link>

                <x-nav-link :href="route('operator.api.status')" :active="request()->routeIs('operator.api.*')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-tower-broadcast w-6 text-center mr-3"></i>
                    <span class="font-medium">Estado APIs</span>
                </x-nav-link>
            @endrole

            <!-- Admin Access (Super Admin Only) -->
            @role('Super Admin')
            <div x-data="{ open: false }" class="mt-4">
                <button @click="open = !open"
                    class="w-full flex items-center px-4 py-3 text-gray-300 hover:text-white hover:bg-white/5 rounded-lg transition-colors duration-200 justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt w-6 text-center mr-3"></i>
                        <span class="font-medium">Admin</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transform transition-transform"
                        :class="{'rotate-180': open}"></i>
                </button>
                <div x-show="open" class="mt-2 pl-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="block px-4 py-2 text-sm text-gray-400 hover:text-white rounded-md hover:bg-white/5">
                        Dashboard Global
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                        class="block px-4 py-2 text-sm text-gray-400 hover:text-white rounded-md hover:bg-white/5">
                        Usuarios
                    </a>
                    <a href="{{ route('admin.api-services.index') }}"
                        class="block px-4 py-2 text-sm text-gray-400 hover:text-white rounded-md hover:bg-white/5">
                        Catálogo APIs
                    </a>
                </div>
            </div>
            @endrole

            @role('Programador')
                {{-- Workflows Links --}}
                <x-nav-link :href="route('programmer.workflows.upload')" :active="request()->routeIs('programmer.workflows.upload')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-cloud-upload-alt w-6 text-center mr-3"></i>
                    <span class="font-medium">Cargar Workflows</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.workflows.history')" :active="request()->routeIs('programmer.workflows.history')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-history w-6 text-center mr-3"></i>
                    <span class="font-medium">Historial Workflows</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.workflows.test')" :active="request()->routeIs('programmer.workflows.test')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-code w-6 text-center mr-3"></i>
                    <span class="font-medium">Testing</span>
                </x-nav-link>

                {{-- Sistema - Logs y Mantenimiento (movido desde Admin) --}}
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Sistema</div>

                <x-nav-link :href="route('programmer.maintenance')" :active="request()->routeIs('programmer.maintenance*')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-tools w-6 text-center mr-3"></i>
                    <span class="font-medium">Mantenimiento</span>
                </x-nav-link>

                <x-nav-link :href="route('programmer.logs')" :active="request()->routeIs('programmer.logs')"
                    class="w-full flex items-center px-4 py-3 rounded-lg transition-colors duration-200">
                    <i class="fas fa-clipboard-list w-6 text-center mr-3"></i>
                    <span class="font-medium">Logs</span>
                </x-nav-link>
            @endrole
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
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex flex-col items-start truncate">
                        <span class="truncate">{{ Auth::user()->name }}</span>
                    </div>
                </div>
                <i class="fas fa-chevron-up text-xs ml-2"></i>
            </button>

            <!-- Dropdown Menu (Upwards) -->
            <div x-show="open" @click.away="open = false"
                class="absolute bottom-full left-0 w-full mb-2 rounded-lg shadow-lg bg-[#0C263B] border border-white/10 overflow-hidden z-50">
                <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 border-b border-white/5">
                    <i class="fas fa-user-edit mr-2"></i> Mi Perfil
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