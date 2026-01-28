<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Seleccione Cliente y Sede</h2>

    <div class="space-y-6">
        <!-- Client Selection -->
        <div>
            <label for="client" class="block text-sm font-medium text-gray-700 mb-2">
                Cliente <span class="text-red-500">*</span>
            </label>
            <select wire:model.live="selectedClientId" id="client"
                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
                <option value="">Seleccione un cliente...</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->company }}</option>
                @endforeach
            </select>
            @error('selectedClientId')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Branch Selection (only shown if client has branches) -->
        @if($selectedClientId && $clientHasBranches)
            <div>
                <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">
                    Sede <span class="text-red-500">*</span>
                </label>
                <select wire:model="selectedBranchId" id="branch"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200">
                    <option value="">Seleccione una sede...</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
                @error('selectedBranchId')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <!-- Info Box: Client without branches -->
        @if($selectedClientId && !$clientHasBranches)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            Este cliente no tiene sedes registradas. El workflow se ejecutara directamente para la empresa central.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Info Box: Ready to continue -->
        @if($selectedClientId && ($selectedBranchId || !$clientHasBranches))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            @if($clientHasBranches)
                                Cliente y sede seleccionados correctamente. Haga clic en "Siguiente" para continuar.
                            @else
                                Cliente seleccionado correctamente. Haga clic en "Siguiente" para continuar.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
