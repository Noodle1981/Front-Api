<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tower-broadcast mr-2 text-brand-dark"></i>Estado de APIs
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Próximamente Card -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8 text-center">
                    <!-- Icono animado -->
                    <div class="mb-6">
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-satellite-dish text-5xl text-blue-500 animate-pulse"></i>
                        </div>
                    </div>
                    
                    <!-- Título -->
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-rocket mr-2 text-purple-500"></i>
                        Próximamente
                    </h3>
                    
                    <!-- Descripción -->
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Estamos trabajando en un panel de estado de APIs en tiempo real. 
                        Pronto podrás ver la salud y disponibilidad de todas las conexiones.
                    </p>
                    
                    <!-- Features que vendrán -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8 text-left">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="flex items-center mb-2">
                                <div class="p-2 bg-green-100 rounded-lg mr-3">
                                    <i class="fas fa-check-circle text-green-600"></i>
                                </div>
                                <span class="font-semibold text-gray-800">Estado en Vivo</span>
                            </div>
                            <p class="text-sm text-gray-500">Monitoreo de conexiones activas</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="flex items-center mb-2">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <i class="fas fa-chart-line text-blue-600"></i>
                                </div>
                                <span class="font-semibold text-gray-800">Métricas</span>
                            </div>
                            <p class="text-sm text-gray-500">Estadísticas de uso y rendimiento</p>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div class="flex items-center mb-2">
                                <div class="p-2 bg-amber-100 rounded-lg mr-3">
                                    <i class="fas fa-bell text-amber-600"></i>
                                </div>
                                <span class="font-semibold text-gray-800">Alertas</span>
                            </div>
                            <p class="text-sm text-gray-500">Notificaciones de problemas</p>
                        </div>
                    </div>
                    
                    <!-- Botón de regreso -->
                    <div class="mt-8">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 bg-brand-dark text-white rounded-lg hover:bg-gray-800 transition">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver al Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
