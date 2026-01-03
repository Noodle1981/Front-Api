<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ejecutar Workflow</h3>
    
    @if($execution && $execution->status === 'success')
        <!-- Execution Success -->
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-green-800">Workflow ejecutado exitosamente</h4>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Fecha: {{ $execution->created_at->format('d/m/Y H:i:s') }}</p>
                        <p>Tiempo de ejecución: {{ $execution->execution_time_ms }} ms</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        @if($execution->logs_json)
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Resultados:</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $execution->logs_json['total_records'] ?? 0 }}</div>
                        <div class="text-xs text-gray-600">Total Registros</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $execution->logs_json['valid_records'] ?? 0 }}</div>
                        <div class="text-xs text-gray-600">Válidos</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $execution->logs_json['invalid_records'] ?? 0 }}</div>
                        <div class="text-xs text-gray-600">Inválidos</div>
                    </div>
                </div>
            </div>
        @endif

    @elseif($execution && $execution->status === 'failed')
        <!-- Execution Failed -->
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-red-800">Error en la ejecución</h4>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ $execution->logs_json['message'] ?? 'Error desconocido' }}</p>
                    </div>
                </div>
            </div>
        </div>

    @elseif($isExecuting)
        <!-- Executing -->
        <div class="bg-purple-50 border-l-4 border-purple-400 p-4 rounded-md">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="animate-spin h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-purple-800">Ejecutando workflow...</p>
                    <p class="text-xs text-purple-600 mt-1">Por favor espere, esto puede tomar unos momentos.</p>
                </div>
            </div>
        </div>

    @else
        <!-- Ready to Execute -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Este batch está listo para ser ejecutado. Haga clic en el botón para iniciar el procesamiento.
                    </p>
                </div>
            </div>
        </div>

        <button wire:click="executeWorkflow" type="button"
            class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Ejecutar Workflow
        </button>
    @endif

    @if($errorMessage)
        <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-medium text-red-800">Error</h4>
                    <p class="text-sm text-red-700 mt-1">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
