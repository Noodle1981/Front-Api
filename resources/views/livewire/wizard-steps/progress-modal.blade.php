{{-- Progress Modal (AlpineJS Version) --}}
<div x-data="{ 
    show: @entangle('showProgressModal'), 
    progress: 0, 
    message: '',
    steps: [
        { label: 'Analizando tipo de archivo...', threshold: 10 },
        { label: 'Analizando archivos...', threshold: 20 },
        { label: 'Analizando contenido...', threshold: 40 },
        { label: 'Ejecutando workflow...', threshold: 50 },
        { label: 'Esperando respuesta del servidor...', threshold: 70 },
        { label: 'Generando reporte...', threshold: 90 }
    ]
}" 
x-on:workflow-progress.window="
    // Handle Livewire 3 event structure (sometimes array, sometimes object)
    let data = Array.isArray($event.detail) ? $event.detail[0] : $event.detail;
    
    // Ensure data exists before accessing
    if (data) {
        show = true; 
        progress = data.percentage || 0; 
        message = data.message || '';
        console.log('Progress Update:', data);
    }
"
x-init="$watch('show', value => {
    if (value) document.body.style.overflow = 'hidden';
    else document.body.style.overflow = 'auto';
})"
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
                    
                    <div class="mt-4">
                        {{-- Progress Bar --}}
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden mb-2">
                            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 h-3 rounded-full transition-all duration-500 ease-out"
                                 :style="`width: ${progress}%`">
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-700" x-text="`${progress}%`"></p>
                    </div>
                    
                    {{-- Progress Steps --}}
                    <div class="mt-6 space-y-3 text-left">
                        <template x-for="step in steps" :key="step.label">
                            <div class="flex items-center text-sm">
                                <template x-if="progress >= step.threshold">
                                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                                <template x-if="progress < step.threshold && message === step.label">
                                    <svg class="animate-spin h-5 w-5 text-purple-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <template x-if="progress < step.threshold && message !== step.label">
                                    <svg class="h-5 w-5 text-gray-300 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                                    </svg>
                                </template>
                                
                                <span :class="{
                                    'text-gray-700 font-medium': progress >= step.threshold,
                                    'text-purple-600 font-semibold': progress < step.threshold && message === step.label,
                                    'text-gray-400': progress < step.threshold && message !== step.label
                                }" x-text="step.label"></span>
                            </div>
                        </template>
                    </div>
                    
                    <p class="mt-6 text-xs text-gray-500 text-center">
                        Por favor espere, este proceso puede tomar unos momentos...
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
