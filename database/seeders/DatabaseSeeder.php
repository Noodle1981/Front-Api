<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles y Permisos (SIEMPRE PRIMERO)
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // 2. Workflows (Tipos y definiciones de archivos)
        $this->call([
            WorkflowTypeSeeder::class,
        ]);

        // 3. Servicios API básicos
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

        // 4. Usuarios básicos
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

        $this->command->info('✅ Seeders básicos completados');
        $this->command->info('');
        $this->command->info('Para datos de prueba completos, ejecuta:');
        $this->command->info('php artisan db:seed --class=CompleteDemoSeeder');
    }
}