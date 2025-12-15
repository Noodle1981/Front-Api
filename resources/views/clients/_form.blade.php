{{-- Layout de dos columnas para una mejor presentación en escritorios --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
    <!-- Nombre -->
    <div class="mb-6">
        <x-input-label for="name" value="Empresa" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $client->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Razón Social -->
    <div class="mb-6">
        <x-input-label for="company" value="Razón Social" />
        <x-text-input id="company" name="company" type="text" class="mt-1 block w-full" :value="old('company', $client->company ?? '')" />
        <x-input-error :messages="$errors->get('company')" class="mt-2" />
    </div>

    <!-- CUIT -->
    <div class="mb-6">
        <x-input-label for="cuit" value="CUIT" />
        <x-text-input id="cuit" name="cuit" type="text" class="mt-1 block w-full" :value="old('cuit', $client->cuit ?? '')" required />
        <x-input-error :messages="$errors->get('cuit')" class="mt-2" />
    </div>

    <!-- Dirección Fiscal -->
    <div class="mb-6">
        <x-input-label for="fiscal_address_street" value="Dirección Fiscal" />
        <x-text-input id="fiscal_address_street" name="fiscal_address_street" type="text" class="mt-1 block w-full" :value="old('fiscal_address_street', $client->fiscal_address_street ?? '')" />
        <x-input-error :messages="$errors->get('fiscal_address_street')" class="mt-2" />
    </div>

    <!-- Actividad Económica -->
    <div class="mb-6">
        <x-input-label for="economic_activity" value="Actividad Económica" />
        <x-text-input id="economic_activity" name="economic_activity" type="text" class="mt-1 block w-full" :value="old('economic_activity', $client->economic_activity ?? '')" />
        <x-input-error :messages="$errors->get('economic_activity')" class="mt-2" />
    </div>

    <!-- ART -->
    <div class="mb-6">
        <x-input-label for="art_provider" value="ART" />
        <x-text-input id="art_provider" name="art_provider" type="text" class="mt-1 block w-full" :value="old('art_provider', $client->art_provider ?? '')" />
        <x-input-error :messages="$errors->get('art_provider')" class="mt-2" />
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

    <!-- Fecha de Alta como Cliente -->
    <div class="mb-6">
        <x-input-label for="registration_date" value="Fecha de Alta como Cliente" />
        <x-text-input id="registration_date" name="registration_date" type="date" class="mt-1 block w-full" :value="old('registration_date', $client->registration_date ?? '')" />
        <x-input-error :messages="$errors->get('registration_date')" class="mt-2" />
    </div>

    <!-- Estado Cliente -->
    <div class="mb-6">
        <x-input-label for="client_status" value="Estado Cliente" />
        <select id="client_status" name="client_status" class="mt-1 block w-full">
            <option value="" disabled selected>Selecciona estado...</option>
            <option value="nuevo" @selected(old('client_status', $client->client_status ?? '') == 'nuevo')>Nuevo</option>
            <option value="activo" @selected(old('client_status', $client->client_status ?? '') == 'activo')>Activo</option>
            <option value="inactivo" @selected(old('client_status', $client->client_status ?? '') == 'inactivo')>Inactivo</option>
        </select>
        <x-input-error :messages="$errors->get('client_status')" class="mt-2" />
    </div>

    <!-- Código de Actividad -->
    <div class="mb-6">
        <x-input-label for="activity_code" value="Código de Actividad" />
        <x-text-input id="activity_code" name="activity_code" type="text" class="mt-1 block w-full" :value="old('activity_code', $client->activity_code ?? '')" />
        <x-input-error :messages="$errors->get('activity_code')" class="mt-2" />
    </div>
</div>

<!-- Notas (ocupa el ancho completo) -->
<div class="mb-6">
    <x-input-label for="notes" value="Notas" />
    <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full bg-gray-900/60 border border-white/10 rounded-lg text-light-text placeholder:text-light-text-muted/50 transition-all duration-300 focus:border-aurora-cyan focus:ring-2 focus:ring-aurora-cyan/40 focus:outline-none" placeholder="Añade información relevante sobre el cliente...">{{ old('notes', $client->notes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>

<!-- Botones de Acción -->
<div class="flex items-center justify-end mt-8 space-x-4">
    <a href="{{ route('clients.index') }}">
        <x-secondary-button type="button">Cancelar</x-secondary-button>
    </a>
    <x-primary-button>{{ $btnText }}</x-primary-button>
</div>
