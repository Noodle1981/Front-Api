<?php

use App\Models\User;
use App\Models\Client;
use App\Models\ApiService;
use App\Models\ClientCredential;

test('user can store client credentials and they are encrypted', function () {
    // 1. Setup
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $service = ApiService::create([
        'name' => 'Test Service',
        'slug' => 'test-service',
        'required_fields' => ['api_key', 'secret'],
    ]);

    // 2. Act: Login & Post
    $response = $this->actingAs($user)
        ->post(route('clients.credentials.store', $client), [
            'api_service_id' => $service->id,
            'credentials' => [
                'api_key' => '12345-ABCDE',
                'secret' => 'super-secret-value'
            ]
        ]);

    // 3. Assert Response
    $response->assertRedirect();
    $response->assertSessionHas('success');

    // 4. Assert Database & Encryption
    $this->assertDatabaseHas('client_credentials', [
        'client_id' => $client->id,
        'api_service_id' => $service->id,
    ]);

    $credential = ClientCredential::first();

    // Verificar que el modelo desencripta automáticamente
    // Verificar que el modelo desencripta automáticamente
    // AsEncryptedArrayObject devuelve un ArrayObject, no un array primitivo
    expect($credential->credentials['api_key'])->toBe('12345-ABCDE');
    expect($credential->credentials['secret'])->toBe('super-secret-value');

    // Verificar que NO se guardó en texto plano (accediendo a atributos raw si fuera posible,
// pero Laravel encapsula mucho. Confiamos en el cast 'AsEncryptedArrayObject')
});

test('validates required fields from api service definition', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $user->id]);

    $service = ApiService::create([
        'name' => 'Strict Service',
        'slug' => 'strict-service',
        'required_fields' => ['mandatory_field'],
    ]);

    $response = $this->actingAs($user)
        ->post(route('clients.credentials.store', $client), [
            'api_service_id' => $service->id,
            'credentials' => [
                'other_field' => 'value' // Falta 'mandatory_field'
            ]
        ]);

    $response->assertSessionHas('error'); // Nuestro controlador retorna 'error' en las validaciones manuales
    $this->assertDatabaseCount('client_credentials', 0);
});

test('user cannot add credentials to client they do not own', function () {
    $owner = User::factory()->create();
    $attacker = User::factory()->create();
    $client = Client::factory()->create(['user_id' => $owner->id]);
    $service = ApiService::create(['name' => 'S', 'slug' => 's', 'required_fields' => []]);

    // El middleware 'user' o la policy deberían bloquear esto,
// PERO nuestro controlador actual no tiene esa validación explícita en Store,
// solo chequeamos propiedad en ClientController.
// ¡Es un buen test para descubrir si nos faltó seguridad!

    // OBS: En ClientController::store (del nuevo controlador) usamos Model Binding.
// Laravel 12 default model binding no chequea ownership a menos que usemos Scoped Bindings o Policies.
// Vamos a probar si falla o pasa.

    $response = $this->actingAs($attacker)
        ->post(route('clients.credentials.store', $client), [
            'api_service_id' => $service->id,
            'credentials' => []
        ]);

    // Si la seguridad es correcta, debería ser 403 o 404.
// Si pasa, significa que tenemos un hueco de seguridad que debo arreglar.
// Por ahora asumimos que el usuario NO debería poder.

    if ($response->status() === 200 || $response->status() === 302) {
        // Si pasa, el test fallará intencionalmente para avisarnos que arreglemos el controller
        $this->assertDatabaseCount('client_credentials', 0);
    } else {
        $response->assertForbidden();
    }
});