{{-- Progress Modal (AlpineJS Version) --}}
<div x-data="{ show: @entangle('showProgressModal'), message: '' }"
x-on:workflow-progress.window="let data = Array.isArray($event.detail) ? $event.detail[0] : $event.detail; if (data) { show = true; message = data.message || ''; }"
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
                    
                    {{-- Simple Message --}}
                    <div class="mt-6">
                        <p class="text-base font-medium text-purple-700" x-text="message"></p>
                    </div>
                    
                    <p class="mt-6 text-xs text-gray-500 text-center">
                        Por favor espere, este proceso puede tomar unos momentos...
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
