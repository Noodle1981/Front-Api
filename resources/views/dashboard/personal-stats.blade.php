<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Estadísticas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Leads -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 bg-opacity-75">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500">Total de Leads</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $data['leads_count'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Deals -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 bg-opacity-75">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500">Total de Deals</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $data['deals_count'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 bg-opacity-75">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500">Total de Tareas</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $data['tasks_count'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>