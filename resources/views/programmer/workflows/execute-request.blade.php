<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-headings font-bold text-xl text-gray-800 leading-tight">
                <i class="fas fa-play-circle mr-2 text-brand-dark"></i> Ejecutar Pedido de Workflow
            </h2>
            <a href="{{ route('programmer.workflows.requests') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-bold rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Pedidos
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Información del Pedido -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6 shadow-lg">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center">
                            <i class="fas fa-info-circle text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Información del Pedido</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600"><i class="fas fa-heading mr-2"></i><strong>Título:</strong></p>
                                <p class="text-sm text-gray-800 ml-6">{{ $request->title }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><i class="fas fa-building mr-2"></i><strong>Cliente:</strong></p>
                                <p class="text-sm text-gray-800 ml-6">{{ $request->client->company }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><i class="fas fa-map-marker-alt mr-2"></i><strong>Sede:</strong></p>
                                <p class="text-sm text-gray-800 ml-6">{{ $request->branch->branch_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><i class="fas fa-project-diagram mr-2"></i><strong>Tipo:</strong></p>
                                <p class="text-sm text-gray-800 ml-6">{{ ucfirst($request->workflow_type) }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600"><i class="fas fa-align-left mr-2"></i><strong>Descripción:</strong></p>
                                <p class="text-sm text-gray-800 ml-6 italic">"{{ $request->description }}"</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><i class="fas fa-user-edit mr-2"></i><strong>Solicitado por:</strong></p>
                                <p class="text-sm text-gray-800 ml-6">{{ $request->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600"><i class="fas fa-calendar-alt mr-2"></i><strong>Fecha de solicitud:</strong></p>
                                <p class="text-sm text-gray-800 ml-6">{{ $request->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wizard de Ejecución -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-upload mr-2 text-brand-dark"></i> Cargar Archivos y Ejecutar
                    </h3>
                    <p class="text-sm text-gray-600 mb-6">
                        Los datos del cliente, sede y tipo de workflow ya están configurados. Solo necesitas cargar los archivos requeridos y ejecutar.
                    </p>
                    
                    @livewire('workflow-file-upload-wizard', ['workflowRequestId' => $request->id])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
