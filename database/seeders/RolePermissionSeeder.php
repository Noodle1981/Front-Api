<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Definir Permisos (Granulares)
        $permissions = [
            // Usuarios
            'manage users',       // Crear/Editar usuarios

            // Clientes
            'view clients',       // Ver listado y detalle
            'create clients',     // Crear clientes
            'edit clients',       // Editar clientes
            'delete clients',     // Soft Delete
            'restore clients',    // Restaurar clientes
            'reassign clients',   // Cambiar de usuario asignado

            // Credenciales (Uso de APIs)
            'manage credentials', // Cargar/Editar tokens de cliente
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Definir Roles y Asignar Permisos

        // A. SUPER ADMIN (IT / Tech Lead)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // B. MANAGER (Gerente)
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'delete clients',
            'restore clients',
            'reassign clients',
        ]);

        // C. PROGRAMADOR (Ex Analista)
        $programador = Role::firstOrCreate(['name' => 'Programador']);
        $programador->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
            'reassign clients',
            'manage credentials',
        ]);

        // D. OPERADOR (Usuario básico)
        $operador = Role::firstOrCreate(['name' => 'Operador']);
        $operador->givePermissionTo([
            'view clients',
            'create clients',
            'edit clients',
        ]);

        // 4. Asignar Super Admin al primer usuario (si existe)
        $user = User::first();
        if ($user) {
            $user->assignRole('Super Admin');
            $this->command->info("Rol Super Admin asignado al usuario: {$user->email}");
        }
    }
}
