{{-- Layout de dos columnas para una mejor presentación en escritorios --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
    <!-- CUIT (Primero porque es la clave) -->
    <div class="mb-6">
        <x-input-label for="cuit" value="CUIT (Sin guiones)" />
        <x-text-input id="cuit" name="cuit" type="text" class="mt-1 block w-full" :value="old('cuit', $client->cuit ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('cuit')" class="mt-2" />
    </div>

    <!-- ESTRUCTURA ORGANIZATIVA -->
    <div class="col-span-1 md:col-span-2 bg-blue-50/50 p-4 rounded-lg border border-blue-100 mb-6"
        x-data="{ parentId: '{{ old('parent_id', $client->parent_id) }}' }">
        <h3 class="font-bold text-gray-700 mb-3 flex items-center">
            <i class="fas fa-sitemap mr-2 text-blue-500"></i> Estructura
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
            <!-- Casa Central / Sede -->
            <div class="mb-4">
                <x-input-label for="parent_id" value="Es Anexo de... (Opcional)" />
                <select id="parent_id" name="parent_id" x-model="parentId"
                    class="mt-1 block w-full bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 backdrop-blur-sm">
                    <option value="">-- Es Casa Central --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}">
                            {{ $parent->company }} (CUIT: {{ $parent->cuit }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
            </div>

            <!-- Nombre de Sucursal -->
            <div class="mb-4" x-show="parentId" x-transition>
                <x-input-label for="branch_name" value="Nombre de Sucursal (Ej: Local Centro)" />
                <x-text-input id="branch_name" name="branch_name" type="text" class="mt-1 block w-full"
                    :value="old('branch_name', $client->branch_name ?? '')" />
                <x-input-error :messages="$errors->get('branch_name')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Razón Social -->
    <div class="mb-6">
        <x-input-label for="company" value="Razón Social" />
        <x-text-input id="company" name="company" type="text" class="mt-1 block w-full" :value="old('company', $client->company ?? '')" required />
        <x-input-error :messages="$errors->get('company')" class="mt-2" />
    </div>

    <!-- Nombre Fantasía -->
    <div class="mb-6">
        <x-input-label for="fantasy_name" value="Nombre Fantasía (Opcional)" />
        <x-text-input id="fantasy_name" name="fantasy_name" type="text" class="mt-1 block w-full"
            :value="old('fantasy_name', $client->fantasy_name ?? '')" />
        <x-input-error :messages="$errors->get('fantasy_name')" class="mt-2" />
    </div>

    <!-- DATOS DE NEGOCIO -->
    <!-- Rubro -->
    <div class="mb-6">
        <x-input-label for="industry" value="Rubro / Industria" />
        <x-text-input id="industry" name="industry" type="text" class="mt-1 block w-full" :value="old('industry', $client->industry ?? '')" placeholder="Ej: Gastronomía" />
        <x-input-error :messages="$errors->get('industry')" class="mt-2" />
    </div>

    <!-- Cantidad de Empleados -->
    <div class="mb-6">
        <x-input-label for="employees_count" value="Cant. Empleados" />
        <x-text-input id="employees_count" name="employees_count" type="number" min="0" class="mt-1 block w-full"
            :value="old('employees_count', $client->employees_count ?? '')" />
        <x-input-error :messages="$errors->get('employees_count')" class="mt-2" />
    </div>

    <!-- Condición Fiscal -->
    <div class="mb-6">
        <x-input-label for="tax_condition" value="Condición Fiscal" />
        <select id="tax_condition" name="tax_condition"
            class="mt-1 block w-full bg-white/50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 backdrop-blur-sm">
            <option value="" disabled selected>Selecciona condición...</option>
            @foreach(['Responsable Inscripto', 'Monotributo', 'Exento', 'Consumidor Final', 'Sujeto No Categorizado'] as $condition)
                <option value="{{ $condition }}" @selected(old('tax_condition', $client->tax_condition ?? '') == $condition)>
                    {{ $condition }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('tax_condition')" class="mt-2" />
    </div>

    <!-- Dirección -->
    <div class="mb-6">
        <x-input-label for="address" value="Dirección" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $client->address ?? '')" />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <!-- Ciudad -->
    <div class="mb-6">
        <x-input-label for="city" value="Ciudad" />
        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $client->city ?? '')" />
        <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>

    <!-- Provincia -->
    <div class="mb-6">
        <x-input-label for="state" value="Provincia" />
        <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" :value="old('state', $client->state ?? '')" />
        <x-input-error :messages="$errors->get('state')" class="mt-2" />
    </div>

    <!-- Código Postal -->
    <div class="mb-6">
        <x-input-label for="zip_code" value="Código Postal" />
        <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code', $client->zip_code ?? '')" />
        <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
    </div>

    <!-- Email -->
    <div class="mb-6">
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $client->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Teléfono -->
    <div class="mb-6">
        <x-input-label for="phone" value="Teléfono" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $client->phone ?? '')" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>
</div>

<!-- Notas Internas (ocupa el ancho completo) -->
<div class="mb-6">
    <x-input-label for="internal_notes" value="Notas Internas" />
    <textarea name="internal_notes" id="internal_notes" rows="4"
        class="mt-1 block w-full bg-white border border-gray-300 rounded-lg text-gray-900 transition-all duration-300 focus:border-aurora-cyan focus:ring-2 focus:ring-aurora-cyan/40 focus:outline-none"
        placeholder="Añade información relevante sobre el cliente...">{{ old('internal_notes', $client->internal_notes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('internal_notes')" class="mt-2" />
</div>

<!-- Botones de Acción -->
<div class="flex items-center justify-end mt-8 space-x-4">
    <a href="{{ route('clients.index') }}">
        <x-secondary-button type="button">Cancelar</x-secondary-button>
    </a>
    <x-primary-button>{{ $btnText }}</x-primary-button>
</div>