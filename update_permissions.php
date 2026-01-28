<?php

/**
 * Script para actualizar permisos del rol Programador
 * Ejecutar con: php update_permissions.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "=== Actualizando permisos del rol Programador ===\n\n";

try {
    // Limpiar cache de permisos
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    echo "Cache de permisos limpiado.\n";

    // Asegurar que el permiso existe
    $deletePermission = Permission::firstOrCreate(['name' => 'delete clients']);
    echo "Permiso 'delete clients' verificado.\n";

    // Obtener el rol Programador
    $programador = Role::where('name', 'Programador')->first();

    if ($programador) {
        // Verificar permisos actuales
        $currentPermissions = $programador->permissions->pluck('name')->toArray();
        echo "Permisos actuales: " . implode(', ', $currentPermissions) . "\n\n";

        // Agregar permiso si no lo tiene
        if (!$programador->hasPermissionTo('delete clients')) {
            $programador->givePermissionTo('delete clients');
            echo "Permiso 'delete clients' agregado al rol Programador.\n";
        } else {
            echo "El rol Programador ya tiene el permiso 'delete clients'.\n";
        }

        // Verificar permisos finales
        $programador->refresh();
        $finalPermissions = $programador->permissions->pluck('name')->toArray();
        echo "\nPermisos finales: " . implode(', ', $finalPermissions) . "\n";
    } else {
        echo "ERROR: No se encontro el rol Programador.\n";
    }

    echo "\n=== Actualizacion completada ===\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
