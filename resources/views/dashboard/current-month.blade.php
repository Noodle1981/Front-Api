<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mi Mes Actual') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Nuevos Leads -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 bg-opacity-75">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500">Nuevos Leads</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $data['new_leads'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Deals Cerrados -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 bg-opacity-75">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500">Deals Cerrados</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $data['closed_deals'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tareas Completadas -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 bg-opacity-75">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-gray-500">Tareas Completadas</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $data['completed_tasks'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>