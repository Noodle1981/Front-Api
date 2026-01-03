<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Batch de Workflow: {{ $batch->batch_code }}
            </h2>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                {{ $batch->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                {{ $batch->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $batch->status === 'validated' ? 'bg-blue-100 text-blue-800' : '' }}
                {{ $batch->status === 'executing' ? 'bg-purple-100 text-purple-800' : '' }}
                {{ $batch->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                {{ ucfirst($batch->status) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Batch Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Batch</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Cliente</p>
                            <p class="font-medium text-gray-900">{{ $batch->client->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Sede</p>
                            <p class="font-medium text-gray-900">{{ $batch->branch->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Workflow</p>
                            <p class="font-medium text-gray-900">{{ $batch->workflowType->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Usuario</p>
                            <p class="font-medium text-gray-900">{{ $batch->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Fecha de Carga</p>
                            <p class="font-medium text-gray-900">{{ $batch->uploaded_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Archivos</p>
                            <p class="font-medium text-gray-900">{{ $batch->uploadedFiles->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Files Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Archivos Cargados</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Original</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamaño</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($batch->uploadedFiles as $index => $file)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $file->fileDefinition->display_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $file->original_filename }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ number_format($file->file_size / 1024, 2) }} KB
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $file->validation_status === 'valid' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $file->validation_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $file->validation_status === 'invalid' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($file->validation_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Execution Panel -->
            @if($batch->status === 'validated' || $batch->status === 'completed' || $batch->status === 'failed')
                @livewire('workflow-execution-panel', ['batch' => $batch])
            @endif

            <div class="mt-6">
                <a href="{{ route('programmer.dashboard') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Volver al Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
