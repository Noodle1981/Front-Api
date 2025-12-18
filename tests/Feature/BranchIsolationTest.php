<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\ApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('credentials are isolated between Headquarters and Branch', function () {
    // 1. Setup: Crear Usuario y API Service
    $user = User::factory()->create();
    $service = ApiService::create([
        'name' => 'Mercado Pago',
        'slug' => 'mercado-pago',
        'required_fields' => ['access_token']
    ]);

    // 2. Crear Estructura: Sede y Sucursal
    $sede = Client::factory()->create([
        'user_id' => $user->id,
        'company' => 'Empresa Madre S.A.',
        'cuit' => '30111111115'
    ]);

    $sucursal = Client::factory()->create([
        'user_id' => $user->id,
        'company' => 'Empresa Madre S.A.', // Puede tener misma razón social
        'branch_name' => 'Local Abasto',
        'cuit' => '30111111115', // Mismo CUIT
        'parent_id' => $sede->id // Vinculado
    ]);

    // 3. Acción: Guardar credenciales DIFERENTES para cada uno
    // Credencial para la SEDE
    $this->actingAs($user)
        ->post(route('clients.credentials.store', $sede), [
            'api_service_id' => $service->id,
            'credentials' => ['access_token' => 'TOKEN_DE_LA_SEDE_123']
        ]);

    // Credencial para la SUCURSAL
    $this->actingAs($user)
        ->post(route('clients.credentials.store', $sucursal), [
            'api_service_id' => $service->id,
            'credentials' => ['access_token' => 'TOKEN_DE_LA_SUCURSAL_456']
        ]);

    // 4. Verificación (Assertions)

    // Recargar modelos para asegurar datos frescos
    $sede->refresh();
    $sucursal->refresh();

    // A. La Sede debe tener SU token
    $credencialSede = $sede->credentials()->first();
    expect($credencialSede->credentials['access_token'])->toBe('TOKEN_DE_LA_SEDE_123');

    // B. La Sucursal debe tener SU token
    $credencialSucursal = $sucursal->credentials()->first();
    expect($credencialSucursal->credentials['access_token'])->toBe('TOKEN_DE_LA_SUCURSAL_456');

    // C. Verificación Cruzada: La Sede NO debe tener el token de la Sucursal
    expect($credencialSede->credentials['access_token'])->not->toBe('TOKEN_DE_LA_SUCURSAL_456');
});
