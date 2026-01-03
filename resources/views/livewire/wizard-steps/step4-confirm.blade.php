<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Revise y Confirme</h2>
    
    <!-- Summary -->
    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de la Carga</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Cliente:</p>
                <p class="font-medium text-gray-900">{{ $clients->find($selectedClientId)->company ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600">Sede:</p>
                <p class="font-medium text-gray-900">{{ $branches->find($selectedBranchId)->branch_name ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600">Tipo de Workflow:</p>
                <p class="font-medium text-gray-900">{{ $selectedWorkflow->name ?? 'N/A' }}</p>
            </div>
            
            <div>
                <p class="text-sm text-gray-600">Archivos Cargados:</p>
                <p class="font-medium text-gray-900">{{ count($uploadedFiles) }} archivos</p>
            </div>
        </div>
    </div>

    <!-- Files List -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Archivos a Procesar</h3>
        
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Archivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Original</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamaño</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($uploadedFiles as $index => $file)
                        @php
                            $definition = $selectedWorkflow->fileDefinitions->find($fileMatches[$index] ?? null);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($definition)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $definition->display_name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        No identificado
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $file->getClientOriginalName() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($file->getSize() / 1024, 2) }} KB
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- JSON Preview (Collapsible) -->
    @if($jsonPreview)
        <div class="mb-6">
            <details class="bg-gray-50 rounded-lg overflow-hidden">
                <summary class="px-6 py-4 cursor-pointer font-medium text-gray-900 hover:bg-gray-100 transition-colors">
                    Ver Preview de JSON Maestro (Basado en archivos cargados)
                </summary>
                <div class="px-6 py-4 bg-gray-900 text-gray-100">
                    <pre class="text-xs overflow-x-auto">{{ json_encode($jsonPreview, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </details>
        </div>
    @endif

    <!-- Confirmation Message -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Al hacer clic en **"EJECUTAR WORKFLOW"**, los archivos se procesarán íntegramente y se generará el JSON maestro para su inspección en la vista de testing.
                </p>
            </div>
        </div>
    </div>
</div>
