<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Cargue los Archivos Requeridos</h2>
    
    @if($selectedWorkflow)
        <!-- File Upload Areas -->
        <div class="space-y-4 mb-6">
            @foreach($selectedWorkflow->fileDefinitions->sortBy('order') as $index => $definition)
                <div class="border-2 border-dashed rounded-lg p-6 transition-all duration-200
                    {{ isset($fileMatches[$index]) && $fileMatches[$index] === $definition->id 
                        ? 'border-green-500 bg-green-50' 
                        : 'border-gray-300 hover:border-purple-400' }}">
                    
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $definition->display_name }}
                                @if($definition->is_required)
                                    <span class="text-red-500">*</span>
                                @endif
                            </h3>
                            
                            @if($definition->description)
                                <p class="text-sm text-gray-600 mt-1">{{ $definition->description }}</p>
                            @endif
                            
                            <!-- Required Columns -->
                            <div class="mt-2 text-xs text-gray-500">
                                <span class="font-medium">Columnas requeridas:</span>
                                {{ $definition->requiredColumns->where('is_required', true)->pluck('column_name')->join(', ') }}
                            </div>
                        </div>
                        
                        <!-- Upload Status -->
                        <div class="ml-4">
                            @php
                                $matchedIndex = array_search($definition->id, $fileMatches);
                            @endphp
                            
                            @if($matchedIndex !== false && isset($uploadedFiles[$matchedIndex]))
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center text-green-600">
                                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-medium">Cargado</span>
                                    </div>
                                    <button wire:click="removeFile({{ $matchedIndex }})" type="button"
                                        class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <div class="text-gray-400 text-sm">Pendiente</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- File Upload Input -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Seleccione archivos para cargar
            </label>
            <input type="file" wire:model="uploadedFiles" multiple accept=".xlsx,.xls,.csv"
                class="block w-full text-sm text-gray-500
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-purple-50 file:text-purple-700
                hover:file:bg-purple-100
                cursor-pointer">
            <p class="mt-1 text-xs text-gray-500">Formatos aceptados: .xlsx, .xls, .csv</p>
            
            <!-- Loading Indicator -->
            <div wire:loading wire:target="uploadedFiles" class="mt-4">
                <div class="flex items-center justify-center p-4 bg-purple-50 rounded-lg">
                    <svg class="animate-spin h-5 w-5 text-purple-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-purple-700">Cargando y validando archivos...</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-3 w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-purple-600 h-2.5 rounded-full animate-pulse" style="width: 100%"></div>
                </div>
            </div>
        </div>

        </div>

        <!-- Simple File Counter -->
        @if(!empty($uploadedFiles))
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-purple-900">
                            {{ count($uploadedFiles) }} archivo(s) seleccionado(s)
                        </span>
                    </div>
                    <span class="text-xs text-purple-600">
                        La validación se realizará al ejecutar el workflow
                    </span>
                </div>
            </div>
        @endif

        <!-- Detailed Progress Bar (shown during processing) -->
        @if($isProcessing)
            <div class="mt-6 bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-6 border border-purple-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="animate-spin h-5 w-5 text-purple-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Procesando Workflow
                </h3>

                <!-- Progress Bar -->
                <div class="mb-4">
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-3 rounded-full transition-all duration-500 ease-out"
                             style="width: {{ $progressPercentage }}%">
                        </div>
                    </div>
                    <div class="text-right mt-1">
                        <span class="text-sm font-medium text-purple-700">{{ $progressPercentage }}%</span>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="space-y-2">
                    @php
                        $steps = [
                            ['label' => 'Validando archivos...', 'threshold' => 5],
                            ['label' => 'Verificando estructura de archivos...', 'threshold' => 10],
                            ['label' => 'Analizando tipo de archivo...', 'threshold' => 15],
                            ['label' => 'Analizando archivos...', 'threshold' => 25],
                            ['label' => 'Analizando contenido...', 'threshold' => 45],
                            ['label' => 'Ejecutando workflow...', 'threshold' => 55],
                            ['label' => 'Esperando respuesta del servidor...', 'threshold' => 75],
                            ['label' => 'Generando reporte...', 'threshold' => 95],
                        ];
                    @endphp

                    @foreach($steps as $step)
                        @php
                            $isComplete = $progressPercentage > $step['threshold'];
                            $isCurrent = $currentProgress === $step['label'];
                            $isFailed = $failedStep === $step['label'];
                        @endphp
                        
                        @if($isComplete || $isCurrent || $isFailed)
                            <div class="flex items-center space-x-2 py-2 px-3 rounded-md transition-all duration-300
                                {{ $isCurrent ? 'bg-white shadow-sm' : '' }}
                                {{ $isFailed ? 'bg-red-50' : '' }}">
                                @if($isFailed)
                                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($isComplete)
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @elseif($isCurrent)
                                    <svg class="animate-spin w-5 h-5 text-purple-600 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                @endif
                                <span class="text-sm {{ $isFailed ? 'font-semibold text-red-700' : ($isCurrent ? 'font-semibold text-purple-700' : 'text-gray-600') }}">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>

{{-- Include Progress Modal --}}
@include('livewire.wizard-steps.progress-modal')
