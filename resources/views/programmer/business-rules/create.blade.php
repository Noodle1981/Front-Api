<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-headings font-bold text-2xl text-gray-800 leading-tight">
                    <i class="fas fa-plus-circle mr-2 text-aurora-cyan"></i> {{ __('Nueva Regla ETL') }}
                </h2>
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
            <form id="rule-form" action="{{ route('programmer.business-rules.store') }}" method="POST" class="mb-6">
                @csrf
                <div class="backdrop-blur-xl bg-white/80 rounded-2xl shadow-lg border border-white/30 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                            <input type="text" name="name" required
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200"
                                   placeholder="Nombre de la regla">
                        </div>
                        
                        {{-- Tipo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Regla</label>
                            <select name="type" id="rule-type"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                                <option value="extraction">Extracción</option>
                                <option value="transformation">Transformación</option>
                                <option value="visualization">Visualización</option>
                            </select>
                        </div>
                        
                        {{-- Cliente --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente (Fuente)</label>
                            <select name="client_id" id="client-select"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->company }}</option>
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
                                    <option value="{{ $api->id }}">{{ $api->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="status"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Endpoint Select (cargado dinámicamente) --}}
                    <div class="mt-4" id="endpoint-container" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Endpoint</label>
                        <select name="endpoint_id" id="endpoint-select"
                                class="w-full md:w-1/3 rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200">
                            <option value="">Seleccionar endpoint...</option>
                        </select>
                    </div>
                    
                    {{-- Descripción --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="2"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-aurora-cyan focus:ring focus:ring-cyan-200"
                                  placeholder="Describe qué hace esta regla..."></textarea>
                    </div>
                </div>
            </form>

            {{-- Alerta: No hay enterprise seleccionada --}}
            <div id="alert-no-source" class="mb-6 backdrop-blur-lg bg-amber-50/80 border-l-4 border-amber-400 p-4 rounded-r-xl shadow-md">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-amber-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-amber-700 font-medium">No hay fuente de datos seleccionada</p>
                        <p class="text-amber-600 text-sm">Selecciona un cliente y servicio API para cargar datos de ejemplo, o escribe tu código Python directamente.</p>
                    </div>
                </div>
            </div>

            {{-- Panel de 3 Columnas --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                
                {{-- Panel 1: Datos de Entrada --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-blue-50/70 to-white/80 rounded-2xl shadow-lg border border-blue-200/30 overflow-hidden">
                    <div class="px-4 py-3 bg-white/50 backdrop-blur-sm border-b border-blue-100/50 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-700">
                            <i class="fas fa-database mr-2 text-blue-500"></i> Datos de Entrada
                        </h3>
                        <div class="flex gap-1">
                            <button type="button" data-tab="json" class="tab-btn px-3 py-1 text-xs rounded-full bg-blue-600 text-white">JSON</button>
                            <button type="button" data-tab="table" class="tab-btn px-3 py-1 text-xs rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300">Tabla</button>
                            <button type="button" data-tab="tree" class="tab-btn px-3 py-1 text-xs rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300">Árbol</button>
                        </div>
                    </div>
                    <div class="p-4" style="height: 450px; overflow: auto;">
                        <div id="tab-json" class="tab-content">
                            <pre id="input-data" class="text-sm bg-gray-900 text-green-400 p-4 rounded-lg overflow-auto h-full font-mono">{
  "sample": true,
  "message": "Selecciona un endpoint para cargar datos",
  "data": []
}</pre>
                        </div>
                        <div id="tab-table" class="tab-content hidden">
                            <div class="text-gray-500 text-sm text-center py-8">
                                <i class="fas fa-table text-3xl mb-3"></i>
                                <p>Vista de tabla próximamente</p>
                            </div>
                        </div>
                        <div id="tab-tree" class="tab-content hidden">
                            <div class="text-gray-500 text-sm text-center py-8">
                                <i class="fas fa-sitemap text-3xl mb-3"></i>
                                <p>Vista de árbol próximamente</p>
                            </div>
                        </div>
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
                            <span class="text-gray-500">Cargando Pyodide...</span>
                        </div>
                    </div>
                    <div style="height: 450px;">
                        <div id="python-editor" style="height: 100%;"></div>
                        <textarea name="python_code" id="python-code-hidden" form="rule-form" style="display: none;"></textarea>
                    </div>
                </div>
                
                {{-- Panel 3: Resultado --}}
                <div class="backdrop-blur-xl bg-gradient-to-br from-green-50/50 to-white/80 rounded-2xl shadow-lg border border-green-200/30 overflow-hidden">
                    <div class="px-4 py-3 bg-white/50 backdrop-blur-sm border-b border-green-100/50">
                        <h3 class="font-semibold text-gray-700">
                            <i class="fas fa-terminal mr-2 text-green-500"></i> Resultado de Ejecución
                        </h3>
                    </div>
                    <div class="p-4" style="height: 450px; overflow: auto;">
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-600 mb-2">Salida:</h4>
                            <pre id="output-result" class="text-sm bg-gray-50 border border-gray-200 p-4 rounded-lg overflow-auto min-h-[150px] font-mono text-gray-700">// El resultado aparecerá aquí</pre>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-600 mb-2">Log:</h4>
                            <pre id="output-log" class="text-sm bg-gray-900 text-gray-300 p-4 rounded-lg overflow-auto min-h-[100px] font-mono">Esperando ejecución...</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monaco Editor CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs/loader.js"></script>
    
    {{-- Pyodide CDN --}}
    <script src="https://cdn.jsdelivr.net/pyodide/v0.25.0/full/pyodide.js"></script>
    
    <script>
        let editor;
        let pyodide = null;
        
        // Código Python inicial
        const defaultCode = `# Regla de Negocio ETL
# Variable disponible: input_data (diccionario con los datos de entrada)

def process(data):
    """
    Procesa los datos de entrada y retorna el resultado.
    """
    # Ejemplo: extraer solo ciertos campos
    result = {
        'processed': True,
        'count': len(data.get('data', [])),
        'items': data.get('data', [])
    }
    return result

# Ejecutar
output = process(input_data)
output`;

        // Inicializar Monaco Editor
        require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.45.0/min/vs' }});
        require(['vs/editor/editor.main'], function() {
            editor = monaco.editor.create(document.getElementById('python-editor'), {
                value: defaultCode,
                language: 'python',
                theme: 'vs-dark',
                minimap: { enabled: false },
                fontSize: 14,
                lineNumbers: 'on',
                scrollBeyondLastLine: false,
                automaticLayout: true,
                wordWrap: 'on'
            });
            
            // Sincronizar con hidden input
            editor.onDidChangeModelContent(() => {
                document.getElementById('python-code-hidden').value = editor.getValue();
            });
            document.getElementById('python-code-hidden').value = editor.getValue();
        });

        // Cargar Pyodide
        async function loadPyodideRuntime() {
            try {
                pyodide = await loadPyodide();
                document.getElementById('pyodide-status').innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-green-600 font-medium">Ready</span>
                `;
                console.log('✓ Pyodide loaded successfully');
            } catch (error) {
                document.getElementById('pyodide-status').innerHTML = `
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-red-600">Error</span>
                `;
                console.error('Pyodide error:', error);
            }
        }
        loadPyodideRuntime();

        // Ejecutar código Python
        document.getElementById('btn-execute').addEventListener('click', async function() {
            if (!pyodide) {
                alert('Pyodide aún está cargando. Por favor espera unos segundos.');
                return;
            }
            
            const code = editor.getValue();
            const inputDataStr = document.getElementById('input-data').textContent;
            
            document.getElementById('output-log').textContent = '⏳ Ejecutando...';
            document.getElementById('output-result').textContent = '';
            
            try {
                // Parsear datos de entrada
                const inputData = JSON.parse(inputDataStr);
                
                // Pasar datos a Python
                pyodide.globals.set('input_data', pyodide.toPy(inputData));
                
                // Ejecutar código
                const result = await pyodide.runPythonAsync(code);
                
                // Mostrar resultado
                let output;
                if (result && typeof result.toJs === 'function') {
                    output = result.toJs();
                } else {
                    output = result;
                }
                
                document.getElementById('output-result').textContent = JSON.stringify(output, null, 2);
                document.getElementById('output-log').textContent = '✓ Ejecutado exitosamente\n' + new Date().toLocaleTimeString();
                
            } catch (error) {
                document.getElementById('output-result').textContent = '';
                document.getElementById('output-log').textContent = '✗ Error:\n' + error.message;
            }
        });

        // Tabs de entrada
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                
                // Actualizar botones
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white');
                    b.classList.add('bg-gray-200', 'text-gray-600');
                });
                this.classList.remove('bg-gray-200', 'text-gray-600');
                this.classList.add('bg-blue-600', 'text-white');
                
                // Mostrar contenido
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                document.getElementById('tab-' + tab).classList.remove('hidden');
            });
        });

        // Cargar endpoints cuando se selecciona API Service
        document.getElementById('api-service-select').addEventListener('change', async function() {
            const apiServiceId = this.value;
            const endpointContainer = document.getElementById('endpoint-container');
            const endpointSelect = document.getElementById('endpoint-select');
            
            if (!apiServiceId) {
                endpointContainer.style.display = 'none';
                document.getElementById('alert-no-source').style.display = 'block';
                return;
            }
            
            try {
                const response = await fetch(`/programadores/reglas/api/services/${apiServiceId}/endpoints`);
                const endpoints = await response.json();
                
                endpointSelect.innerHTML = '<option value="">Seleccionar endpoint...</option>';
                endpoints.forEach(ep => {
                    endpointSelect.innerHTML += `<option value="${ep.id}">${ep.method} - ${ep.name}</option>`;
                });
                
                endpointContainer.style.display = 'block';
                document.getElementById('alert-no-source').style.display = 'none';
            } catch (error) {
                console.error('Error cargando endpoints:', error);
            }
        });

        // Cargar datos de ejemplo cuando se selecciona endpoint
        document.getElementById('endpoint-select').addEventListener('change', async function() {
            const endpointId = this.value;
            
            if (!endpointId) return;
            
            try {
                const response = await fetch(`/programadores/reglas/api/input-data?endpoint_id=${endpointId}`);
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('input-data').textContent = JSON.stringify(result.data, null, 2);
                }
            } catch (error) {
                console.error('Error cargando datos:', error);
            }
        });
    </script>
</x-app-layout>
