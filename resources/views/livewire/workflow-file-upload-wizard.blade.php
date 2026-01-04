<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Cargar Archivos de Workflow</h1>
            <p class="mt-2 text-sm text-gray-600">Siga los pasos para cargar y validar sus archivos</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                @foreach([
                    1 => 'Cliente/Sede',
                    2 => 'Workflow',
                    3 => 'Archivos & Ejecución'
                ] as $step => $label)
                    <div class="flex-1 {{ $step < 3 ? 'relative' : '' }}">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-300
                                    {{ $currentStep > $step ? 'bg-green-500 text-white' : '' }}
                                    {{ $currentStep === $step ? 'bg-purple-600 text-white ring-4 ring-purple-200' : '' }}
                                    {{ $currentStep < $step ? 'bg-gray-200 text-gray-500' : '' }}">
                                    @if($currentStep > $step)
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        {{ $step }}
                                    @endif
                                </div>
                            </div>
                            @if($step < 3)
                                <div class="flex-1 h-0.5 mx-2 transition-all duration-300
                                    {{ $currentStep > $step ? 'bg-green-500' : 'bg-gray-200' }}">
                                </div>
                            @endif
                        </div>
                        <div class="mt-2 text-xs font-medium text-center
                            {{ $currentStep === $step ? 'text-purple-600' : 'text-gray-500' }}">
                            {{ $label }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Step Content -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-6">
            @if($currentStep === 1)
                @include('livewire.wizard-steps.step1-client-branch')
            @elseif($currentStep === 2)
                @include('livewire.wizard-steps.step2-workflow')
            @elseif($currentStep === 3)
                @include('livewire.wizard-steps.step3-files')
            @endif
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between">
            @if($currentStep > 1 && !$isProcessing)
                <button wire:click="previousStep" type="button"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Anterior
                </button>
            @else
                <div></div>
            @endif

            @if($currentStep < 3)
                <button wire:click="nextStep" type="button"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="nextStep">Siguiente</span>
                    <span wire:loading wire:target="nextStep" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Validando...
                    </span>
                    <svg wire:loading.remove wire:target="nextStep" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @else
                <button wire:click="submitBatch" type="button"
                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    @if($isProcessing) disabled @endif>
                    @if($isProcessing)
                        <span class="flex items-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ $currentProgress }}
                        </span>
                    @else
                        <span wire:loading.remove wire:target="submitBatch" class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            EJECUTAR WORKFLOW
                        </span>
                        <span wire:loading wire:target="submitBatch" class="flex items-center">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Iniciando...
                        </span>
                    @endif
                </button>
            @endif
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Hay errores que debe corregir:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
