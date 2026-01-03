<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Workflow Testing - Últimas Ejecuciones
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @forelse($executions as $execution)
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $execution->fileBatch->batch_code }}</h3>
                            <p class="text-sm text-gray-400">{{ $execution->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $execution->status === 'success' ? 'bg-green-900 text-green-200' : '' }}
                            {{ $execution->status === 'failed' ? 'bg-red-900 text-red-200' : '' }}">
                            {{ ucfirst($execution->status) }}
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="grid grid-cols-4 gap-4 mb-4 text-sm">
                        <div>
                            <p class="text-gray-500">Cliente</p>
                            <p class="text-white">{{ $execution->fileBatch->client->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Workflow</p>
                            <p class="text-white">{{ $execution->fileBatch->workflowType->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Usuario</p>
                            <p class="text-white">{{ $execution->fileBatch->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Tiempo</p>
                            <p class="text-white">{{ $execution->execution_time_ms }} ms</p>
                        </div>
                    </div>

                    <!-- JSON Sent -->
                    <details class="mb-4">
                        <summary class="cursor-pointer text-purple-400 hover:text-purple-300 font-medium mb-2">
                            JSON Enviado
                        </summary>
                        <div class="bg-gray-900 rounded p-4 overflow-x-auto">
                            <pre class="text-xs text-green-400"><code>{{ json_encode($execution->json_sent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </details>

                    <!-- JSON Response -->
                    <details>
                        <summary class="cursor-pointer text-purple-400 hover:text-purple-300 font-medium mb-2">
                            JSON Respuesta
                        </summary>
                        <div class="bg-gray-900 rounded p-4 overflow-x-auto">
                            <pre class="text-xs text-blue-400"><code>{{ json_encode($execution->json_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    </details>
                </div>
            @empty
                <div class="bg-gray-800 rounded-lg shadow-lg p-6 text-center">
                    <p class="text-gray-400">No hay ejecuciones disponibles</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
