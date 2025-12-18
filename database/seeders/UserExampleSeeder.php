<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientCredential;
use App\Models\ApiService;
use Illuminate\Support\Facades\Hash;

class UserExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Creando usuario user@example.com...');

        // Verificar si ya existe
        $user = User::where('email', 'user@example.com')->first();
        
        if ($user) {
            $this->command->warn('⚠️  El usuario user@example.com ya existe. Actualizando rol...');
        } else {
            // Crear usuario
            $user = User::create([
                'name' => 'Usuario Ejemplo',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $this->command->info('✅ Usuario creado: user@example.com');
        }

        // Asignar rol User
        $user->syncRoles(['User']);
        $this->command->info('✅ Rol "User" asignado');

        // Obtener servicios API
        $afip = ApiService::where('name', 'AFIP')->first();
        $mercadoPago = ApiService::where('name', 'Mercado Pago')->first();

        // Crear 2 clientes de ejemplo
        $clientsData = [
            [
                'cuit' => '30-12345678-9',
                'company' => 'Empresa de Prueba S.A.',
                'fantasy_name' => 'Prueba SA',
                'tax_condition' => 'Responsable Inscripto',
                'industry' => 'Comercio',
                'employees_count' => 10,
                'email' => 'contacto@prueba.com',
                'phone' => '+54 11 1234-5678',
                'address' => 'Av. Ejemplo 123',
                'city' => 'Buenos Aires',
                'state' => 'CABA',
                'zip_code' => '1000',
            ],
            [
                'cuit' => '33-98765432-1',
                'company' => 'Comercio Test S.R.L.',
                'fantasy_name' => 'Test SRL',
                'tax_condition' => 'Responsable Inscripto',
                'industry' => 'Servicios',
                'employees_count' => 5,
                'email' => 'info@test.com',
                'phone' => '+54 11 8765-4321',
                'address' => 'Calle Falsa 456',
                'city' => 'Buenos Aires',
                'state' => 'CABA',
                'zip_code' => '1001',
            ],
        ];

        foreach ($clientsData as $clientData) {
            $client = Client::create([
                'user_id' => $user->id,
                'cuit' => $clientData['cuit'],
                'company' => $clientData['company'],
                'fantasy_name' => $clientData['fantasy_name'],
                'tax_condition' => $clientData['tax_condition'],
                'industry' => $clientData['industry'],
                'employees_count' => $clientData['employees_count'],
                'email' => $clientData['email'],
                'phone' => $clientData['phone'],
                'address' => $clientData['address'],
                'city' => $clientData['city'],
                'state' => $clientData['state'],
                'zip_code' => $clientData['zip_code'],
                'active' => true,
            ]);

            $this->command->info("  📍 Cliente creado: {$client->company}");

            // Crear credencial AFIP
            if ($afip) {
                ClientCredential::create([
                    'client_id' => $client->id,
                    'api_service_id' => $afip->id,
                    'credentials' => [
                        'cuit' => $clientData['cuit'],
                        'certificate' => 'demo_cert_' . $client->id,
                        'private_key' => 'demo_key_' . $client->id,
                    ],
                    'is_active' => true,
                    'execution_frequency' => 'daily',
                    'alert_email' => $clientData['email'],
                ]);
            }
        }

        $this->command->info("\n🎉 ¡Usuario user@example.com configurado exitosamente!");
        $this->command->info("📊 Email: user@example.com");
        $this->command->info("🔑 Password: password");
        $this->command->info("👤 Rol: User (Contador)");
        $this->command->info("🏢 Clientes: " . $user->clients()->count());
    }
}
