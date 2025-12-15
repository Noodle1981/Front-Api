<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Client;
use App\Models\Establishment;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Task;
use App\Models\Lead;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seguridad: solo sembrar en entorno local por defecto
        if (!app()->environment('local')) {
            $this->command?->warn('DemoDataSeeder: entorno no local detectado. No se insertaron datos.');
            return;
        }

        DB::transaction(function () {
            // 1) Usuario no admin para asignaciones (crea uno si no existiera)
            $user = User::where('is_admin', false)->first();
            if (!$user) {
                $user = User::first();
            }
            if (!$user) {
                $user = User::firstOrCreate(
                    ['email' => 'demo.user+crm@example.com'],
                    [
                        'name' => 'Usuario Demo CRM',
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                        'remember_token' => Str::random(10),
                        'is_admin' => false,
                    ]
                );
            }

            // 2) Etapas de negocio (no destructivo, por nombre)
            $stages = [
                ['name' => 'Descubrimiento', 'order' => 0],
                ['name' => 'Propuesta Enviada', 'order' => 1],
                ['name' => 'Negociación', 'order' => 2],
                ['name' => 'Cierre', 'order' => 3],
            ];
            foreach ($stages as $s) {
                DealStage::firstOrCreate(['name' => $s['name']], ['order' => $s['order']]);
            }
            $propuesta = DealStage::where('name', 'Propuesta Enviada')->first();

            // 3) Cliente demo (idempotente por CUIT)
            $client = Client::firstOrCreate(
                ['cuit' => '20-12345678-9'],
                [
                    'name' => 'ACME Argentina',
                    'company' => 'ACME SA',
                    'website' => 'https://acme.example.com',
                    'email' => 'contacto@acme.example.com',
                    'phone' => '+54 11 4000 0000',
                    'economic_activity' => 'Servicios H&S',
                    'notes' => 'Cliente demo creado por seeder.',
                    'active' => true,
                    'user_id' => $user->id,
                ]
            );

            // 4) Establecimiento demo (idempotente por cliente+nombre)
            $est = Establishment::firstOrCreate(
                ['client_id' => $client->id, 'name' => 'Planta Pilar'],
                [
                    'address_street' => 'Ruta 8 km 56',
                    'address_city' => 'Pilar',
                    'address_state' => 'Buenos Aires',
                    'address_country' => 'Argentina',
                    'notes' => 'Sede demo del cliente ACME.',
                    'active' => true,
                ]
            );

            // 5) Contacto demo (idempotente por cliente+email)
            $contact = Contact::firstOrCreate(
                ['client_id' => $client->id, 'email' => 'maria.referente+demo@example.com'],
                [
                    'name' => 'María Referente',
                    'phone' => '+54 351 555 5555',
                    'position' => 'Jefa H&S',
                    'notes' => 'Contacto demo creado por seeder.',
                    'establishment_id' => $est->id,
                    'active' => true,
                ]
            );

            // 6) Lead demo (idempotente por email único)
            $lead = Lead::firstOrCreate(
                ['email' => 'juan.test+demo@example.com'],
                [
                    'name' => 'Juan Test',
                    'company' => 'ACME SA',
                    'phone' => '+54 9 11 2345 6789',
                    'source' => 'Web',
                    'status' => 'nuevo',
                    'user_id' => $user->id,
                    'notes' => 'Lead demo para pruebas funcionales.',
                ]
            );

            // 7) Deal demo (idempotente por nombre+cliente)
            $deal = Deal::firstOrCreate(
                ['name' => 'Implementación Servicio H&S', 'client_id' => $client->id],
                [
                    'value' => 1500000.00,
                    'user_id' => $user->id,
                    'deal_stage_id' => optional($propuesta)->id ?? DealStage::first()->id,
                    'expected_close_date' => now()->addDays(30)->toDateString(),
                    'status' => 'open',
                    // Evitamos escribir en columnas que podrían no existir en BD legacy
                ]
            );

            // 8) Tareas demo (polimórficas) - idempotentes por título + target
            Task::firstOrCreate(
                ['title' => 'Enviar propuesta', 'taskable_id' => $deal->id, 'taskable_type' => Deal::class],
                [
                    'description' => 'Enviar propuesta inicial a cliente.',
                    'status' => 'pendiente',
                    'due_date' => now()->addDays(3)->toDateString(),
                    'user_id' => $user->id,
                ]
            );

            Task::firstOrCreate(
                ['title' => 'Llamar a cliente', 'taskable_id' => $client->id, 'taskable_type' => Client::class],
                [
                    'description' => 'Coordinar demo con el cliente.',
                    'status' => 'pendiente',
                    'due_date' => now()->addDays(2)->toDateString(),
                    'user_id' => $user->id,
                ]
            );

            // 9) Actividades demo (idempotentes por tipo+deal)
            \App\Models\Activity::firstOrCreate([
                'type' => 'note',
                'loggable_id' => $deal->id,
                'loggable_type' => Deal::class,
                'user_id' => $user->id,
                'description' => 'Primera nota de seguimiento.',
            ], [
                'status' => 'pendiente',
                'deal_stage_id' => $deal->deal_stage_id ?? 1,
                'details' => json_encode(['origen' => 'seeder']),
            ]);

            \App\Models\Activity::firstOrCreate([
                'type' => 'call',
                'loggable_id' => $deal->id,
                'loggable_type' => Deal::class,
                'user_id' => $user->id,
                'description' => 'Llamada de prueba al cliente.',
            ], [
                'status' => 'completada',
                'deal_stage_id' => $deal->deal_stage_id ?? 1,
                'details' => json_encode(['duracion' => '10min', 'origen' => 'seeder']),
            ]);

            \App\Models\Activity::firstOrCreate([
                'type' => 'meeting',
                'loggable_id' => $deal->id,
                'loggable_type' => Deal::class,
                'user_id' => $user->id,
                'description' => 'Reunión agendada para demo.',
            ], [
                'status' => 'en espera',
                'deal_stage_id' => $deal->deal_stage_id ?? 1,
                'details' => json_encode(['fecha' => now()->addDays(5)->toDateString(), 'origen' => 'seeder']),
            ]);
        });

        $this->command?->info('DemoDataSeeder: datos de prueba listos (idempotente, solo local).');
    }
}
