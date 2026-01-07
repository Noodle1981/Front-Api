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


        <!-- Validation Feedback -->
        @if(!empty($uploadedFiles))
            @php
                $expectedCount = $selectedWorkflow->expected_files_count ?? 0;
                $uploadedCount = count($uploadedFiles);
                $hasErrors = !empty($validationErrors);
                $isValid = !$hasErrors && $uploadedCount === $expectedCount;
            @endphp
            
            <div class="rounded-lg p-4 border {{ $isValid ? 'bg-green-50 border-green-200' : ($hasErrors ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200') }}">
                <div class="space-y-2">
                    <!-- File Count -->
                    <div class="flex items-center">
                        @if($isValid)
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @elseif($hasErrors)
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                        <span class="text-sm font-medium {{ $isValid ? 'text-green-900' : ($hasErrors ? 'text-red-900' : 'text-yellow-900') }}">
                            {{ $uploadedCount }}/{{ $expectedCount }} archivo(s) cargado(s)
                        </span>
                    </div>
                    
                    <!-- Validation Status -->
                    <div class="flex items-center">
                        @if($isValid)
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-green-900">Archivos correctos</span>
                        @elseif($hasErrors)
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-red-900">Errores de validación</span>
                        @else
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-yellow-900">Validando archivos...</span>
                        @endif
                    </div>
                    
                    <!-- Required Columns Status -->
                    @if($isValid)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-green-900">Campos obligatorios correctos</span>
                        </div>
                        
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium text-green-900">Archivos validados</span>
                        </div>
                    @endif
                    
                    <!-- Error Details -->
                    @if($hasErrors)
                        <div class="mt-2 pt-2 border-t border-red-300">
                            <ul class="text-xs text-red-700 space-y-1">
                                @foreach($validationErrors as $error)
                                    <li>• {{ $error['message'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

{{-- Include Progress Modal --}}
@include('livewire.wizard-steps.progress-modal')
