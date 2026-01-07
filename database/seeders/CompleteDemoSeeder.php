<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompleteDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Generando Datos Completos de Demo...');

        // Obtener servicios API existentes
        $afip = ApiService::where('name', 'AFIP')->first();
        $mercadoPago = ApiService::where('name', 'Mercado Pago')->first();

        // Datos realistas de contadores
        $contadores = [
            [
                'name' => 'María González',
                'email' => 'maria.gonzalez@demo.com',
                'clients_data' => [
                    [
                        'type' => 'headquarters',
                        'cuit' => '30-71234567-8',
                        'company' => 'Distribuidora San Martín S.A.',
                        'fantasy_name' => 'Distribuidora San Martín',
                        'tax_condition' => 'Responsable Inscripto',
                        'industry' => 'Comercio Mayorista',
                        'employees_count' => 45,
                        'email' => 'admin@sanmartin.com.ar',
                        'phone' => '+54 11 4567-8900',
                        'website' => 'www.sanmartin.com.ar',
                        'address' => 'Av. Corrientes 1234',
                        'city' => 'Buenos Aires',
                        'state' => 'CABA',
                        'zip_code' => '1043',
                        'branches' => [
                            [
                                'branch_name' => 'Sucursal Palermo',
                                'cuit' => '30-71234567-8',
                                'address' => 'Av. Santa Fe 3456',
                                'city' => 'Buenos Aires',
                                'state' => 'CABA',
                                'zip_code' => '1425',
                                'phone' => '+54 11 4567-8901',
                            ],
                            [
                                'branch_name' => 'Sucursal Belgrano',
                                'cuit' => '30-71234567-8',
                                'address' => 'Av. Cabildo 2345',
                                'city' => 'Buenos Aires',
                                'state' => 'CABA',
                                'zip_code' => '1428',
                                'phone' => '+54 11 4567-8902',
                            ],
                        ],
                    ],
                    [
                        'type' => 'headquarters',
                        'cuit' => '33-65432198-9',
                        'company' => 'Textil del Norte S.R.L.',
                        'fantasy_name' => 'Textil Norte',
                        'tax_condition' => 'Responsable Inscripto',
                        'industry' => 'Industria Textil',
                        'employees_count' => 28,
                        'email' => 'contacto@textilnorte.com',
                        'phone' => '+54 381 456-7890',
                        'website' => 'www.textilnorte.com',
                        'address' => 'Ruta 9 Km 1234',
                        'city' => 'San Miguel de Tucumán',
                        'state' => 'Tucumán',
                        'zip_code' => '4000',
                    ],
                ],
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@demo.com',
                'clients_data' => [
                    [
                        'type' => 'headquarters',
                        'cuit' => '30-98765432-1',
                        'company' => 'Alimentos Frescos del Sur S.A.',
                        'fantasy_name' => 'Frescos del Sur',
                        'tax_condition' => 'Responsable Inscripto',
                        'industry' => 'Alimentación',
                        'employees_count' => 120,
                        'email' => 'info@frescossur.com.ar',
                        'phone' => '+54 261 789-0123',
                        'website' => 'www.frescossur.com.ar',
                        'address' => 'Parque Industrial Lote 45',
                        'city' => 'Mendoza',
                        'state' => 'Mendoza',
                        'zip_code' => '5500',
                        'branches' => [
                            [
                                'branch_name' => 'Depósito Godoy Cruz',
                                'cuit' => '30-98765432-1',
                                'address' => 'Av. San Martín 789',
                                'city' => 'Godoy Cruz',
                                'state' => 'Mendoza',
                                'zip_code' => '5501',
                                'phone' => '+54 261 789-0124',
                            ],
                        ],
                    ],
                    [
                        'type' => 'headquarters',
                        'cuit' => '27-34567890-2',
                        'company' => 'Servicios Contables López',
                        'fantasy_name' => 'Estudio López',
                        'tax_condition' => 'Monotributo',
                        'industry' => 'Servicios Profesionales',
                        'employees_count' => 5,
                        'email' => 'estudio@lopezcontadores.com',
                        'phone' => '+54 261 456-7890',
                        'address' => 'San Martín 456 Piso 3',
                        'city' => 'Mendoza',
                        'state' => 'Mendoza',
                        'zip_code' => '5500',
                    ],
                ],
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@demo.com',
                'clients_data' => [
                    [
                        'type' => 'headquarters',
                        'cuit' => '30-55667788-9',
                        'company' => 'Construcciones del Litoral S.A.',
                        'fantasy_name' => 'Construcciones Litoral',
                        'tax_condition' => 'Responsable Inscripto',
                        'industry' => 'Construcción',
                        'employees_count' => 85,
                        'email' => 'obras@litoral.com.ar',
                        'phone' => '+54 342 567-8901',
                        'website' => 'www.construccioneslitoral.com.ar',
                        'address' => 'Bv. Pellegrini 2345',
                        'city' => 'Santa Fe',
                        'state' => 'Santa Fe',
                        'zip_code' => '3000',
                        'branches' => [
                            [
                                'branch_name' => 'Oficina Rosario',
                                'cuit' => '30-55667788-9',
                                'address' => 'Av. Belgrano 1234',
                                'city' => 'Rosario',
                                'state' => 'Santa Fe',
                                'zip_code' => '2000',
                                'phone' => '+54 341 567-8902',
                            ],
                            [
                                'branch_name' => 'Depósito Paraná',
                                'cuit' => '30-55667788-9',
                                'address' => 'Ruta 11 Km 456',
                                'city' => 'Paraná',
                                'state' => 'Entre Ríos',
                                'zip_code' => '3100',
                                'phone' => '+54 343 567-8903',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Roberto Fernández',
                'email' => 'roberto.fernandez@demo.com',
                'clients_data' => [
                    [
                        'type' => 'headquarters',
                        'cuit' => '30-44556677-3',
                        'company' => 'Tecnología Avanzada S.A.',
                        'fantasy_name' => 'TechAvanzada',
                        'tax_condition' => 'Responsable Inscripto',
                        'industry' => 'Tecnología',
                        'employees_count' => 35,
                        'email' => 'contacto@techavanzada.com',
                        'phone' => '+54 11 5678-9012',
                        'website' => 'www.techavanzada.com',
                        'address' => 'Av. del Libertador 5678',
                        'city' => 'Buenos Aires',
                        'state' => 'CABA',
                        'zip_code' => '1426',
                    ],
                    [
                        'type' => 'headquarters',
                        'cuit' => '33-22334455-6',
                        'company' => 'Farmacia Central S.R.L.',
                        'fantasy_name' => 'Farmacia Central',
                        'tax_condition' => 'Responsable Inscripto',
                        'industry' => 'Salud',
                        'employees_count' => 12,
                        'email' => 'ventas@farmaciacentral.com',
                        'phone' => '+54 11 4321-5678',
                        'address' => 'Av. Rivadavia 9876',
                        'city' => 'Buenos Aires',
                        'state' => 'CABA',
                        'zip_code' => '1406',
                    ],
                ],
            ],
            [
                'name' => 'Laura Sánchez',
                'email' => 'laura.sanchez@demo.com',
                'clients_data' => [
                    [
                        'type' => 'headquarters',
                        'cuit' => '30-11223344-5',
                        'company' => 'Logística Integral del Oeste S.A.',
                        'fantasy_name' => 'LogiOeste',
                        'tax_condition' => 'Responsable Inscripto',
                        'industry' => 'Logística y Transporte',
                        'employees_count' => 95,
                        'email' => 'operaciones@logioeste.com.ar',
                        'phone' => '+54 11 6789-0123',
                        'website' => 'www.logioeste.com.ar',
                        'address' => 'Ruta 3 Km 45',
                        'city' => 'La Matanza',
                        'state' => 'Buenos Aires',
                        'zip_code' => '1755',
                        'branches' => [
                            [
                                'branch_name' => 'Base Morón',
                                'cuit' => '30-11223344-5',
                                'address' => 'Av. Gaona 3456',
                                'city' => 'Morón',
                                'state' => 'Buenos Aires',
                                'zip_code' => '1708',
                                'phone' => '+54 11 6789-0124',
                            ],
                        ],
                    ],
                    [
                        'type' => 'headquarters',
                        'cuit' => '27-88776655-4',
                        'company' => 'Consultoría Empresarial Moderna',
                        'fantasy_name' => 'CEM Consultores',
                        'tax_condition' => 'Monotributo',
                        'industry' => 'Consultoría',
                        'employees_count' => 3,
                        'email' => 'info@cemconsultores.com',
                        'phone' => '+54 11 4567-1234',
                        'address' => 'Av. Córdoba 1234 Of. 5',
                        'city' => 'Buenos Aires',
                        'state' => 'CABA',
                        'zip_code' => '1055',
                    ],
                ],
            ],
        ];

        // 1. Crear SUPER ADMIN
        $this->command->info('🔧 Creando Super Admin (admin@example.com)...');
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Super Admin']);
        $this->command->info("✅ Super Admin creado y rol asignado.");

        // 2. Crear PROGRAMADOR (Ex Analista)
        $this->command->info('🔧 Creando Programador (analista@example.com)...');
        $analista = User::firstOrCreate(
            ['email' => 'analista@example.com'],
            [
                'name' => 'Programador Principal',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $analista->syncRoles(['Programador']);
        $this->command->info("✅ Programador creado y rol asignado.");

        // 3. Crear usuario user@example.com (para demos)
        $this->command->info('🔧 Creando usuario user@example.com...');
        
        $exampleUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Usuario Ejemplo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $exampleUser->syncRoles(['Operador']);
        $this->command->info("✅ Usuario creado: {$exampleUser->email}");

        // Crear 2 clientes para user@example.com
        $exampleClientsData = [
            // Cliente 1: Empresa grande con sucursales
            [
                'type' => 'headquarters',
                'cuit' => '30-12345678-9',
                'company' => 'Distribuidora Central S.A.',
                'fantasy_name' => 'Distribuidora Central',
                'tax_condition' => 'Responsable Inscripto',
                'industry' => 'Comercio Mayorista',
                'employees_count' => 45,
                'email' => 'admin@distribuidoracentral.com',
                'phone' => '+54 11 4567-8900',
                'website' => 'www.distribuidoracentral.com',
                'address' => 'Av. Corrientes 1500',
                'city' => 'Buenos Aires',
                'state' => 'CABA',
                'zip_code' => '1043',
                'branches' => [
                    [
                        'branch_name' => 'Sucursal Palermo',
                        'cuit' => '30-12345678-9',
                        'address' => 'Av. Santa Fe 3200',
                        'city' => 'Buenos Aires',
                        'state' => 'CABA',
                        'zip_code' => '1425',
                        'phone' => '+54 11 4567-8901',
                    ],
                    [
                        'branch_name' => 'Sucursal Belgrano',
                        'cuit' => '30-12345678-9',
                        'address' => 'Av. Cabildo 2100',
                        'city' => 'Buenos Aires',
                        'state' => 'CABA',
                        'zip_code' => '1428',
                        'phone' => '+54 11 4567-8902',
                    ],
                ],
            ],
            // Cliente 2: Empresa mediana
            [
                'type' => 'headquarters',
                'cuit' => '33-98765432-1',
                'company' => 'Tecnología Avanzada S.R.L.',
                'fantasy_name' => 'TechAvanzada',
                'tax_condition' => 'Responsable Inscripto',
                'industry' => 'Tecnología',
                'employees_count' => 25,
                'email' => 'contacto@techavanzada.com',
                'phone' => '+54 11 5678-9012',
                'website' => 'www.techavanzada.com',
                'address' => 'Av. del Libertador 5678',
                'city' => 'Buenos Aires',
                'state' => 'CABA',
                'zip_code' => '1426',
            ],
            // Cliente 3: Comercio con sucursal
            [
                'type' => 'headquarters',
                'cuit' => '30-55667788-9',
                'company' => 'Supermercado del Norte S.A.',
                'fantasy_name' => 'Super Norte',
                'tax_condition' => 'Responsable Inscripto',
                'industry' => 'Comercio Minorista',
                'employees_count' => 60,
                'email' => 'ventas@supernorte.com',
                'phone' => '+54 11 6789-0123',
                'website' => 'www.supernorte.com',
                'address' => 'Av. Rivadavia 8900',
                'city' => 'Buenos Aires',
                'state' => 'CABA',
                'zip_code' => '1406',
                'branches' => [
                    [
                        'branch_name' => 'Sucursal Caballito',
                        'cuit' => '30-55667788-9',
                        'address' => 'Av. Acoyte 456',
                        'city' => 'Buenos Aires',
                        'state' => 'CABA',
                        'zip_code' => '1405',
                        'phone' => '+54 11 6789-0124',
                    ],
                ],
            ],
            // Cliente 4: Servicios profesionales
            [
                'type' => 'headquarters',
                'cuit' => '27-34567890-2',
                'company' => 'Estudio Jurídico Asociados',
                'fantasy_name' => 'EJA Abogados',
                'tax_condition' => 'Responsable Inscripto',
                'industry' => 'Servicios Profesionales',
                'employees_count' => 12,
                'email' => 'info@ejaabogados.com',
                'phone' => '+54 11 4321-5678',
                'address' => 'Av. Córdoba 1234 Piso 8',
                'city' => 'Buenos Aires',
                'state' => 'CABA',
                'zip_code' => '1055',
            ],
            // Cliente 5: Industria con sucursal
            [
                'type' => 'headquarters',
                'cuit' => '30-11223344-5',
                'company' => 'Metalúrgica Industrial S.A.',
                'fantasy_name' => 'MetalInd',
                'tax_condition' => 'Responsable Inscripto',
                'industry' => 'Industria Manufacturera',
                'employees_count' => 80,
                'email' => 'produccion@metalind.com',
                'phone' => '+54 11 7890-1234',
                'website' => 'www.metalind.com',
                'address' => 'Parque Industrial Lote 15',
                'city' => 'La Matanza',
                'state' => 'Buenos Aires',
                'zip_code' => '1755',
                'branches' => [
                    [
                        'branch_name' => 'Depósito Morón',
                        'cuit' => '30-11223344-5',
                        'address' => 'Av. Gaona 2345',
                        'city' => 'Morón',
                        'state' => 'Buenos Aires',
                        'zip_code' => '1708',
                        'phone' => '+54 11 7890-1235',
                    ],
                ],
            ],
        ];

        foreach ($exampleClientsData as $clientData) {
            // Crear sede principal
            $headquarters = Client::create([
                'user_id' => $exampleUser->id,
                'cuit' => $clientData['cuit'],
                'company' => $clientData['company'],
                'fantasy_name' => $clientData['fantasy_name'],
                'tax_condition' => $clientData['tax_condition'],
                'industry' => $clientData['industry'],
                'employees_count' => $clientData['employees_count'],
                'email' => $clientData['email'],
                'phone' => $clientData['phone'],
                'website' => $clientData['website'] ?? null,
                'address' => $clientData['address'],
                'city' => $clientData['city'],
                'state' => $clientData['state'],
                'zip_code' => $clientData['zip_code'],
                'active' => true,
            ]);

            $this->command->info("  📍 Cliente creado: {$headquarters->company}");



            // Crear sucursales si existen
            if (isset($clientData['branches'])) {
                foreach ($clientData['branches'] as $branchData) {
                    $branch = Client::create([
                        'user_id' => $exampleUser->id,
                        'parent_id' => $headquarters->id,
                        'branch_name' => $branchData['branch_name'],
                        'cuit' => $branchData['cuit'],
                        'company' => $headquarters->company,
                        'fantasy_name' => $headquarters->fantasy_name,
                        'tax_condition' => $headquarters->tax_condition,
                        'industry' => $headquarters->industry,
                        'employees_count' => rand(5, 20),
                        'email' => strtolower(str_replace(' ', '', $branchData['branch_name'])) . '@' . explode('@', $clientData['email'])[1],
                        'phone' => $branchData['phone'],
                        'address' => $branchData['address'],
                        'city' => $branchData['city'],
                        'state' => $branchData['state'],
                        'zip_code' => $branchData['zip_code'],
                        'active' => true,
                    ]);

                    $this->command->info("    🏢 Sucursal creada: {$branch->branch_name}");
                }
            }


        }

        $this->command->info('');

        foreach ($contadores as $contadorData) {
            // Crear usuario contador
            $user = User::create([
                'name' => $contadorData['name'],
                'email' => $contadorData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            $user->assignRole('Operador');

            $this->command->info("✅ Usuario creado: {$user->email}");

            // Crear clientes y sucursales
            foreach ($contadorData['clients_data'] as $clientData) {
                // Crear sede principal
                $headquarters = Client::create([
                    'user_id' => $user->id,
                    'cuit' => $clientData['cuit'],
                    'company' => $clientData['company'],
                    'fantasy_name' => $clientData['fantasy_name'],
                    'tax_condition' => $clientData['tax_condition'],
                    'industry' => $clientData['industry'],
                    'employees_count' => $clientData['employees_count'],
                    'email' => $clientData['email'],
                    'phone' => $clientData['phone'],
                    'website' => $clientData['website'] ?? null,
                    'address' => $clientData['address'],
                    'city' => $clientData['city'],
                    'state' => $clientData['state'],
                    'zip_code' => $clientData['zip_code'],
                    'active' => true,
                ]);

                $this->command->info("  📍 Sede creada: {$headquarters->company}");



                // Crear sucursales si existen
                if (isset($clientData['branches'])) {
                    foreach ($clientData['branches'] as $branchData) {
                        $branch = Client::create([
                            'user_id' => $user->id,
                            'parent_id' => $headquarters->id,
                            'branch_name' => $branchData['branch_name'],
                            'cuit' => $branchData['cuit'],
                            'company' => $headquarters->company,
                            'fantasy_name' => $headquarters->fantasy_name,
                            'tax_condition' => $headquarters->tax_condition,
                            'industry' => $headquarters->industry,
                            'employees_count' => rand(5, 20),
                            'email' => strtolower(str_replace(' ', '', $branchData['branch_name'])) . '@' . explode('@', $clientData['email'])[1],
                            'phone' => $branchData['phone'],
                            'address' => $branchData['address'],
                            'city' => $branchData['city'],
                            'state' => $branchData['state'],
                            'zip_code' => $branchData['zip_code'],
                            'active' => true,
                        ]);

                        $this->command->info("    🏢 Sucursal creada: {$branch->branch_name}");
                    }
                }


            }
        }

        $this->command->info("\n🎉 ¡Datos Completos de Demo Generados Exitosamente!");
        $this->command->info("\n📊 RESUMEN:");
        $this->command->info("👥 Total de usuarios: " . User::role('Operador')->count());
        $this->command->info("🏢 Total de clientes (sedes): " . Client::whereNull('parent_id')->count());
        $this->command->info("🏪 Total de sucursales: " . Client::whereNotNull('parent_id')->count());

        
        $this->command->info("\n🔐 CREDENCIALES DE ACCESO:");
        $this->command->info("📧 user@example.com / password (Operador con 5 sedes + 4 sucursales)");
        $this->command->info("📧 analista@example.com / password (Programador Principal)");
        $this->command->info("📧 admin@example.com / password (Super Admin)");
        $this->command->info("📧 maria.gonzalez@demo.com / password123 (Operador)");
    }


}
