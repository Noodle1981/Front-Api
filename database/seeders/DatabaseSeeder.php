<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            // ClientSeeder::class, // (Optional if we had it)
        ]);

        // Solo crear usuarios admin y user
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_admin' => true,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_admin' => false,
        ]);

        // Servicios API Iniciales
        \App\Models\ApiService::create([
            'name' => 'Mercado Pago',
            'slug' => 'mercado-pago',
            'base_url' => 'https://api.mercadopago.com',
            'required_fields' => ['public_key', 'access_token'],
        ]);

        \App\Models\ApiService::create([
            'name' => 'AFIP Facturación',
            'slug' => 'afip-wsfe',
            'base_url' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
            'required_fields' => ['cuit_representada', 'certificado_crt', 'clave_privada_key'],
        ]);
    }
}