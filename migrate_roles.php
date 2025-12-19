<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Iniciando migración de roles...\n";

// 1. Migrar Analistas a Programadores
$analistas = User::role('Analista')->get();
$countAnalistas = $analistas->count();
echo "Encontrados $countAnalistas usuarios con rol 'Analista'.\n";

if ($countAnalistas > 0) {
    echo "Migrando Analistas a Programadores...\n";
    foreach ($analistas as $user) {
        $user->removeRole('Analista');
        $user->assignRole('Programador');
        echo " - Usuario {$user->email} actualizado a Programador.\n";
    }
}

// 2. Migrar Users a Operadores
$users = User::role('User')->get();
$countUsers = $users->count();
echo "Encontrados $countUsers usuarios con rol 'User'.\n";

if ($countUsers > 0) {
    echo "Migrando Users a Operadores...\n";
    foreach ($users as $user) {
        $user->removeRole('User');
        $user->assignRole('Operador');
        echo " - Usuario {$user->email} actualizado a Operador.\n";
    }
}

// 3. Limpieza (Opcional - por seguridad no borramos los roles viejos aun, solo quitamos asignaciones)
// $roleAnalista = Role::where('name', 'Analista')->first();
// if ($roleAnalista && $roleAnalista->users()->count() == 0) {
//     $roleAnalista->delete();
//     echo "Rol 'Analista' eliminado (ya no tiene usuarios).\n";
// }

echo "---------------------------------------------------\n";
echo "Migración completada.\n";
echo "Total Analistas -> Programadores: $countAnalistas\n";
echo "Total Users -> Operadores: $countUsers\n";
