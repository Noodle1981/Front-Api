{{-- Progress/Error Modal (AlpineJS Version) --}}
<div x-data="{ show: @entangle('showProgressModal'), message: '', failed: @entangle('failedStep'), errorMessage: '' }"
    x-on:workflow-progress.window="let data = Array.isArray($event.detail) ? $event.detail[0] : $event.detail; if (data) { show = true; message = data.message || ''; }"
    x-on:workflow-error.window="let data = Array.isArray($event.detail) ? $event.detail[0] : $event.detail; if (data) { show = true; failed = data.step || 'Error'; errorMessage = data.message || ''; }"
    x-init="$watch('show', value => { if (value) document.body.style.overflow = 'hidden'; else document.body.style.overflow = 'auto'; })"
    x-show="show"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true">

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

    {{-- Modal Content --}}
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative inline-block align-middle bg-white rounded-lg px-8 pt-6 pb-8 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">

            {{-- Error State --}}
            <template x-if="failed">
                <div>
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                        <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>

                    <div class="mt-4 text-center">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">
                            Error en el Proceso
                        </h3>

                        <div class="mt-4">
                            <p class="text-sm font-medium text-red-600" x-text="failed"></p>
                        </div>

                        {{-- Error Details --}}
                        <div class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200 text-left max-h-48 overflow-y-auto">
                            <p class="text-sm text-red-700" x-text="errorMessage || 'Ha ocurrido un error durante la ejecución del workflow.'"></p>

                            {{-- Show Livewire errors if any --}}
                            @if($errors->any())
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        <div class="mt-6 flex justify-center space-x-3">
                            <button type="button"
                                    wire:click="closeProgressModal"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Cerrar
                            </button>
                            <button type="button"
                                    wire:click="retryWorkflow"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reintentar
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Loading State --}}
            <template x-if="!failed">
                <div>
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-purple-100">
                        <svg class="animate-spin h-10 w-10 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div class="mt-4 text-center">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">
                            Procesando Workflow
                        </h3>

                        {{-- Progress Bar --}}
                        <div class="mt-6">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-purple-600 h-2.5 rounded-full transition-all duration-300"
                                     style="width: {{ $progressPercentage ?? 0 }}%"></div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">{{ $progressPercentage ?? 0 }}%</p>
                        </div>

                        {{-- Simple Message --}}
                        <div class="mt-4">
                            <p class="text-base font-medium text-purple-700" x-text="message"></p>
                        </div>

                        <p class="mt-6 text-xs text-gray-500 text-center">
                            Por favor espere, este proceso puede tomar unos momentos...
                        </p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
