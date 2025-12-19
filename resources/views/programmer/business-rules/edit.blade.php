<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-headings font-bold text-2xl text-gray-800 leading-tight">
                    <i class="fas fa-edit mr-2 text-aurora-cyan"></i> {{ __('Editar Regla ETL') }}
                </h2>
                <p class="text-gray-500 text-sm mt-1">{{ $rule->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" id="btn-execute" 
                        class="inline-flex items-center px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-full font-bold text-sm uppercase tracking-widest transition">
                    <i class="fas fa-play mr-2"></i> Ejecutar
                </button>
                <button type="submit" form="rule-form"
                        class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-btn-start to-btn-end hover:to-btn-start text-white rounded-full font-bold text-sm uppercase tracking-widest shadow-[0_4px_14px_0_rgba(247,131,143,0.39)] transition">
                    <i class="fas fa-save mr-2"></i> Guardar
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Formulario de Configuración --}}
            <form id="rule-form" action="{{ route('programmer.business-rules.update', $rule) }}" method="POST" class="mb-6">
                @csrf
                @method('PUT')
                <div class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg border border-white/30 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input type="text" name="name" required value="{{ $rule->name }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                        </div>
                        
                        {{-- Tipo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Regla</label>
                            <select name="type" id="rule-type"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                                <option value="extraction" {{ $rule->type === 'extraction' ? 'selected' : '' }}>Extracción</option>
                                <option value="transformation" {{ $rule->type === 'transformation' ? 'selected' : '' }}>Transformación</option>
                                <option value="visualization" {{ $rule->type === 'visualization' ? 'selected' : '' }}>Visualización</option>
                            </select>
                        </div>
                        
                        {{-- Cliente --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente (Fuente)</label>
                            <select name="client_id" id="client-select"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $rule->client_id == $client->id ? 'selected' : '' }}>{{ $client->company }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- API Service --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Servicio API</label>
                            <select name="api_service_id" id="api-service-select"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                                <option value="">Seleccionar API...</option>
                                @foreach($apiServices as $api)
                                    <option value="{{ $api->id }}" {{ $rule->api_service_id == $api->id ? 'selected' : '' }}>{{ $api->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                                <option value="active" {{ $rule->status === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ $rule->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Endpoint Select --}}
                    <div class="mt-4" id="endpoint-container" style="{{ $rule->api_service_id ? '' : 'display: none;' }}">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Endpoint</label>
                        <select name="endpoint_id" id="endpoint-select"
                                class="w-full md:w-1/3 rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                            <option value="">Seleccionar endpoint...</option>
                            @foreach($endpoints as $ep)
                                <option value="{{ $ep->id }}" {{ $rule->endpoint_id == $ep->id ? 'selected' : '' }}>{{ $ep->method }} - {{ $ep->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- Descripción --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="2"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">{{ $rule->description }}</textarea>
                    </div>
                </div>
            </form>

            {{-- Panel de 3 Columnas --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                
                {{-- Panel 1: Datos de Entrada --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-blue-50/70 to-white/80 rounded-2xl shadow-lg border border-blue-200/30 overflow-hidden">
                    <div class="px-4 py-3 bg-white/50 backdrop-blur-sm border-b border-blue-100/50">
                        <h3 class="font-semibold text-gray-700">
                            <i class="fas fa-database mr-2 text-blue-500"></i> Datos de Entrada
                        </h3>
                    </div>
                    <div class="p-4" style="height: 450px; overflow: auto;">
                        <pre id="input-data" class="text-sm bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto h-full font-mono">{
  "sample": true,
  "message": "Datos de ejemplo"
}</pre>
                    </div>
                </div>
                
                {{-- Panel 2: Editor Python --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-yellow-50/50 to-white/80 rounded-2xl shadow-lg border border-yellow-200/30 overflow-hidden">
                    <div class="px-4 py-3 bg-white/50 backdrop-blur-sm border-b border-yellow-100/50 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-700">
                            <i class="fab fa-python mr-2 text-yellow-500"></i> Editor de Código Python
                        </h3>
                        <div id="pyodide-status" class="flex items-center gap-2 text-xs">
                            <span class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></span>
                            <span class="text-gray-500">Cargando...</span>
                        </div>
                    </div>
                    <div style="height: 450px;">
                        <div id="python-editor" style="height: 100%;"></div>
                        <textarea name="python_code" id="python-code-hidden" form="rule-form" style="display: none;">{{ $rule->python_code }}</textarea>
                    </div>
                </div>
                
                {{-- Panel 3: Resultado --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-green-50/50 to-white/80 rounded-2xl shadow-lg border border-green-200/30 overflow-hidden">
                    <div class="px-4 py-3 bg-white/50 backdrop-blur-sm border-b border-green-100/50">
                        <h3 class="font-semibold text-gray-700">
                            <i class="fas fa-terminal mr-2 text-green-500"></i> Resultado
                        </h3>
                    </div>
                    <div class="p-4" style="height: 450px; overflow: auto;">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-600 mb-2">Salida:</h4>
                            <pre id="output-result" class="text-sm bg-gray-50 border border-gray-200 p-4 rounded-lg min-h-[150px] font-mono">// Resultado</pre>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-600 mb-2">Log:</h4>
                            <pre id="output-log" class="text-sm bg-gray-900 text-gray-300 p-4 rounded-lg min-h-[100px] font-mono">Esperando...</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs/loader.js"></script>
    <script src="https://cdn.jsdelivr.net/pyodide/v0.25.0/full/pyodide.js"></script>
    
    <script>
        let editor;
        let pyodide = null;
        
        const existingCode = @json($rule->python_code);

        require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            editor = monaco.editor.create(document.getElementById('python-editor'), {
                value: existingCode,
                language: 'python',
                theme: 'vs-dark',
                minimap: { enabled: false },
                fontSize: 14,
                automaticLayout: true,
                wordWrap: 'on'
            });
            
            editor.onDidChangeModelContent(() => {
                document.getElementById('python-code-hidden').value = editor.getValue();
            });
        });

        async function loadPyodideRuntime() {
            try {
                pyodide = await loadPyodide();
                document.getElementById('pyodide-status').innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-green-600 font-medium">Ready</span>
                `;
            } catch (error) {
                document.getElementById('pyodide-status').innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-red-600">Error</span>
                `;
            }
        }
        loadPyodideRuntime();

        document.getElementById('btn-execute').addEventListener('click', async function() {
            if (!pyodide) {
                alert('Pyodide cargando...');
                return;
            }
            
            const code = editor.getValue();
            const inputDataStr = document.getElementById('input-data').textContent;
            
            document.getElementById('output-log').textContent = '⏳ Ejecutando...';
            
            try {
                const inputData = JSON.parse(inputDataStr);
                pyodide.globals.set('input_data', pyodide.toPy(inputData));
                const result = await pyodide.runPythonAsync(code);
                
                let output = result && typeof result.toJs === 'function' ? result.toJs() : result;
                document.getElementById('output-result').textContent = JSON.stringify(output, null, 2);
                document.getElementById('output-log').textContent = '✓ OK - ' + new Date().toLocaleTimeString();
            } catch (error) {
                document.getElementById('output-log').textContent = '✗ Error: ' + error.message;
            }
        });
    </script>
</x-app-layout>
