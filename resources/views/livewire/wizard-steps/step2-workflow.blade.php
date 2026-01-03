<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Seleccione el Tipo de Workflow</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($workflowTypes as $workflowType)
            <div wire:click="$set('selectedWorkflowTypeId', {{ $workflowType->id }})"
                class="relative cursor-pointer rounded-lg border-2 p-6 transition-all duration-200 hover:shadow-lg
                {{ $selectedWorkflowTypeId === $workflowType->id 
                    ? 'border-purple-600 bg-purple-50 ring-2 ring-purple-600' 
                    : 'border-gray-200 hover:border-purple-300' }}">
                
                <!-- Selection Indicator -->
                @if($selectedWorkflowTypeId === $workflowType->id)
                    <div class="absolute top-4 right-4">
                        <div class="w-6 h-6 bg-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                @endif
                
                <!-- Icon -->
                <div class="mb-4">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Content -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ $workflowType->name }}
                </h3>
                
                @if($workflowType->description)
                    <p class="text-sm text-gray-600 mb-4">
                        {{ $workflowType->description }}
                    </p>
                @endif
                
                <!-- Files Count -->
                <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    {{ $workflowType->expected_files_count }} archivos requeridos
                </div>
            </div>
        @endforeach
    </div>

    @error('selectedWorkflowTypeId')
        <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <!-- Info Box -->
    @if($selectedWorkflowTypeId)
        <div class="mt-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        Workflow seleccionado. Haga clic en "Siguiente" para cargar los archivos.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
