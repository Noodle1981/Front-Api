<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Generar Arqueo Mensual</h1>
            <p class="mt-2 text-sm text-gray-600">Seleccione la empresa y el período para generar el arqueo</p>
        </div>

        <!-- Main Form Card -->
        <div class="bg-white rounded-lg shadow-xl p-8 mb-6">
            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Errores encontrados</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 1: Empresa -->
            <div class="mb-8">
                <label for="client" class="block text-sm font-medium text-gray-700 mb-2">
                    Empresa <span class="text-red-500">*</span>
                </label>
                <select wire:model.live="selectedClientId" id="client"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                    <option value="">Seleccione una empresa...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->company }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Step 2: Sucursal (opcional) -->
            @if($branches->isNotEmpty())
                <div class="mb-8">
                    <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">
                        Sucursal (opcional)
                    </label>
                    <select wire:model.live="selectedBranchId" id="branch"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                        <option value="">Todas las sucursales</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Step 3: Período -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Período <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="year" class="block text-xs text-gray-500 mb-1">Año</label>
                        <select wire:model.live="selectedYear" id="year"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="month" class="block text-xs text-gray-500 mb-1">Mes</label>
                        <select wire:model.live="selectedMonth" id="month"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm">
                            @foreach($months as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Available Data Info -->
            @if($selectedClientId)
                <div class="mb-8 p-4 rounded-lg {{ $availableExecutionsCount > 0 ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                    <div class="flex items-center">
                        @if($availableExecutionsCount > 0)
                            <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-700 text-sm">
                                <strong>{{ $availableExecutionsCount }}</strong> ejecuciones de conciliación disponibles para el período seleccionado
                            </span>
                        @else
                            <svg class="h-5 w-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-yellow-700 text-sm">
                                No hay datos de conciliación para el período seleccionado
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Available Months Quick Select -->
                @if(!empty($availableMonths))
                    <div class="mb-8">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Meses con datos disponibles:</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_slice($availableMonths, 0, 6) as $monthData)
                                <button type="button"
                                    wire:click="$set('selectedYear', {{ $monthData['year'] }}); $set('selectedMonth', {{ $monthData['month'] }})"
                                    class="px-3 py-1 text-xs rounded-full transition-colors
                                        {{ $selectedYear == $monthData['year'] && $selectedMonth == $monthData['month']
                                            ? 'bg-purple-600 text-white'
                                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    {{ $monthData['label'] }} ({{ $monthData['count'] }})
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button wire:click="submitArqueo" type="button"
                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                    @if($isProcessing || $availableExecutionsCount === 0) disabled @endif>
                    @if($isProcessing)
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generando arqueo...
                    @else
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generar Arqueo
                    @endif
                </button>
            </div>
        </div>

        <!-- Help Text -->
        <div class="text-center text-sm text-gray-500">
            <p>El arqueo se genera a partir de los datos de conciliación almacenados en el sistema.</p>
            <p class="mt-1">Primero debe ejecutar las conciliaciones para el período deseado.</p>
        </div>
    </div>

    <!-- Progress/Error Modal -->
    @if($showProgressModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50"
             x-data="{ errorMessage: '' }"
             x-on:workflow-error.window="errorMessage = $event.detail[0]?.message || $event.detail?.message || ''">
            <div class="bg-white rounded-lg shadow-xl p-8 max-w-lg w-full mx-4">
                @if($failedStep)
                    <!-- Error State -->
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                            <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Error en el Proceso</h3>
                        <p class="text-sm font-medium text-red-600 mb-4">{{ $failedStep }}</p>

                        <!-- Error Details -->
                        <div class="p-4 bg-red-50 rounded-lg border border-red-200 text-left max-h-48 overflow-y-auto mb-6">
                            @if($errors->any())
                                <ul class="text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-red-700" x-text="errorMessage || 'Ha ocurrido un error durante la generación del arqueo.'"></p>
                            @endif
                        </div>

                        <div class="flex justify-center space-x-3">
                            <button wire:click="closeProgressModal" type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Cerrar
                            </button>
                            <button wire:click="retryWorkflow" type="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reintentar
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Loading State -->
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100 mb-4">
                            <svg class="animate-spin h-10 w-10 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Procesando Arqueo</h3>
                        <p class="text-base font-medium text-purple-700 mb-4">{{ $currentProgress }}</p>

                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                            <div class="bg-purple-600 h-2.5 rounded-full transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500">{{ $progressPercentage }}%</p>

                        <p class="mt-6 text-xs text-gray-400">
                            Por favor espere, este proceso puede tomar unos momentos...
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
