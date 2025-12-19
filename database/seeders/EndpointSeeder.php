<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiService;
use App\Models\Endpoint;

class EndpointSeeder extends Seeder
{
    public function run()
    {
        // Mercado Pago
        $mp = ApiService::where('slug', 'mercado-pago')->first();
        if ($mp) {
            $endpoints = [
                [
                    'method' => 'GET',
                    'url' => '/v1/payment_methods',
                    'description' => 'Obtener métodos de pago disponibles',
                    'parameters' => ['public_key' => 'string'],
                ],
                [
                    'method' => 'POST',
                    'url' => '/v1/payments',
                    'description' => 'Crear un nuevo pago',
                    'parameters' => ['transaction_amount' => 'float', 'token' => 'string', 'installments' => 'integer', 'payment_method_id' => 'string', 'payer' => 'object'],
                ],
                [
                    'method' => 'GET',
                    'url' => '/v1/payments/{id}',
                    'description' => 'Obtener información de un pago por ID',
                    'parameters' => ['id' => 'integer'],
                ],
                [
                    'method' => 'POST',
                    'url' => '/oauth/token',
                    'description' => 'Obtener Token de Acceso (OAuth)',
                    'parameters' => ['client_id' => 'string', 'client_secret' => 'string', 'grant_type' => 'string'],
                ],
            ];

            foreach ($endpoints as $data) {
                Endpoint::firstOrCreate(
                    ['api_service_id' => $mp->id, 'method' => $data['method'], 'url' => $data['url']],
                    $data
                );
            }
        }

        // Ualá Bis
        $uala = ApiService::where('slug', 'uala-bis')->first();
        if ($uala) {
            $endpoints = [
                [
                    'method' => 'POST',
                    'url' => '/auth/token',
                    'description' => 'Autenticación y obtención de token',
                    'parameters' => ['user_name' => 'string', 'client_id' => 'string', 'client_secret' => 'string', 'grant_type' => 'string'],
                ],
                [
                    'method' => 'POST',
                    'url' => '/orders',
                    'description' => 'Crear una orden de cobro',
                    'parameters' => ['amount' => 'float', 'description' => 'string', 'callback_fail' => 'url', 'callback_success' => 'url'],
                ],
                [
                    'method' => 'GET',
                    'url' => '/orders/{id}',
                    'description' => 'Consultar estado de orden',
                    'parameters' => ['id' => 'string'],
                ],
            ];

            foreach ($endpoints as $data) {
                Endpoint::firstOrCreate(
                    ['api_service_id' => $uala->id, 'method' => $data['method'], 'url' => $data['url']],
                    $data
                );
            }
        }

        // Naranja X
        $nx = ApiService::where('slug', 'naranja-x')->first();
        if ($nx) {
            $endpoints = [
                [
                    'method' => 'POST',
                    'url' => '/oauth/token',
                    'description' => 'Autenticación OAuth2',
                    'parameters' => ['client_id' => 'string', 'client_secret' => 'string', 'grant_type' => 'string', 'scope' => 'string'],
                ],
                [
                    'method' => 'POST',
                    'url' => '/payment-intents',
                    'description' => 'Generar intención de pago',
                    'parameters' => ['amount' => 'integer', 'currency' => 'string'],
                ],
            ];

            foreach ($endpoints as $data) {
                Endpoint::firstOrCreate(
                    ['api_service_id' => $nx->id, 'method' => $data['method'], 'url' => $data['url']],
                    $data
                );
            }
        }
    }
}
