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
        $services = [
            [
                'name' => 'AFIP Facturación',
                'slug' => 'afip-wsfe',
                'base_url' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
                'required_fields' => ['cuit', 'certificado_crt', 'clave_privada_key', 'punto_venta'],
            ],
            [
                'name' => 'Mercado Pago',
                'slug' => 'mercado-pago',
                'base_url' => 'https://api.mercadopago.com/v1',
                'required_fields' => ['public_key', 'access_token', 'client_id', 'client_secret'],
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

        foreach ($services as $data) {
            ApiService::firstOrCreate(
                ['slug' => $data['slug']], 
                $data
            );
        }
    }
}
