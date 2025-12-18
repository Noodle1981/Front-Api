<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\ApiService;
use App\Models\ClientCredential;
use App\Models\ApiLog;
use App\Models\Transaction;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Generando Datos de Demo...');

        // Asegurar Rol User
        if (!Role::where('name', 'User')->exists()) {
            Role::create(['name' => 'User']);
        }

        $apiServices = ApiService::all();
        if ($apiServices->isEmpty()) {
            $this->command->error('No hay ApiServices creados. Ejecuta DatabaseSeeder primero.');
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $email = "user{$i}@demo.com";
            
            // 1. Crear Usuario
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Usuario Demo {$i}",
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                ]
            );
            $user->syncRoles(['User']);
            $this->command->info("Usuario creado: {$email}");

            // 2. Asignar 1-5 Clientes
            $clientCount = rand(1, 5);
            $clients = Client::factory()->count($clientCount)->create([
                'user_id' => $user->id
            ]);

            foreach ($clients as $client) {
                // 3. Conectar 1-3 Servicios por Cliente
                $servicesToConnect = $apiServices->random(rand(1, min(3, $apiServices->count())));

                foreach ($servicesToConnect as $service) {
                    // Credencial Fake
                    ClientCredential::create([
                        'client_id' => $client->id,
                        'api_service_id' => $service->id,
                        'credentials' => ['access_token' => 'demo_token_' . rand(1000,9999)],
                        'is_active' => true,
                    ]);

                    // 4. Generar Logs (Historial 30 días)
                    for ($j = 0; $j < rand(10, 30); $j++) {
                        $date = Carbon::now()->subDays(rand(0, 30))->subMinutes(rand(0, 1440));
                        $isError = rand(1, 100) > 85; // 15% chance error

                        ApiLog::create([
                            'client_id' => $client->id,
                            'api_service_id' => $service->id,
                            'status' => $isError ? 'error' : 'success',
                            'event_type' => $isError ? 'Connection Failed' : 'Data Sync',
                            'details' => $isError ? json_encode(['error' => 'Timeout', 'code' => 500]) : json_encode(['records' => rand(1,50)]),
                            'happened_at' => $date,
                        ]);
                    }

                    // 5. Generar Transacciones (Historial 30 días)
                    for ($k = 0; $k < rand(5, 20); $k++) {
                        $date = Carbon::now()->subDays(rand(0, 30));
                        $amount = rand(1000, 50000);
                        
                        Transaction::create([
                            'client_id' => $client->id,
                            'api_service_id' => $service->id,
                            'date_at' => $date,
                            'amount' => $amount,
                            'currency' => 'ARS',
                            'type' => rand(0,1) ? 'income' : 'expense',
                            'description' => "Movimiento Demo #{$k}",
                            'status' => 'verified',
                            'raw_data' => ['id' => uniqid(), 'sandbox' => true]
                        ]);
                    }
                }
            }
        }
        
        $this->command->info('¡Demo Data Generada Exitosamente!');
    }
}
