<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiService;

class ApiServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Mercado Pago',
                'slug' => 'mercado-pago',
                'base_url' => 'https://api.mercadopago.com',
                'logo_url' => 'img/logosApis/mercadopago.png',
                'required_fields' => ['public_key', 'access_token', 'client_id', 'client_secret'],
            ],
            [
                'name' => 'Ualá Bis',
                'slug' => 'uala-bis',
                'base_url' => 'https://api.uala.com.ar',
                'logo_url' => 'img/logosApis/Uala.png',
                'required_fields' => ['user_name', 'client_id', 'client_secret'],
            ],
            [
                'name' => 'Naranja X',
                'slug' => 'naranja-x',
                'base_url' => 'https://api.naranjax.com',
                'logo_url' => 'img/logosApis/naranjax.png',
                'required_fields' => ['client_id', 'client_secret', 'grant_type'],
            ],
            [
                'name' => 'Google Ads',
                'slug' => 'google-ads',
                'base_url' => 'https://googleads.googleapis.com',
                'required_fields' => ['developer_token', 'customer_id', 'client_id', 'client_secret', 'refresh_token'],
            ],
            [
                'name' => 'Tienda Nube',
                'slug' => 'tienda-nube',
                'base_url' => 'https://api.tiendanube.com/v1',
                'required_fields' => ['store_id', 'access_token', 'user_agent'],
            ],
        ];

        foreach ($providers as $data) {
            ApiService::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
