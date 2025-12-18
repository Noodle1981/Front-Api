<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USUARIOS Y ROLES ===\n\n";

$users = App\Models\User::all();

foreach ($users as $user) {
    echo "Email: {$user->email}\n";
    echo "Roles: " . $user->getRoleNames()->implode(', ') . "\n";
    echo "Permisos: " . $user->getAllPermissions()->pluck('name')->implode(', ') . "\n";
    echo "---\n";
}

echo "\n=== ROLES Y SUS PERMISOS ===\n\n";

$roles = Spatie\Permission\Models\Role::with('permissions')->get();

foreach ($roles as $role) {
    echo "Rol: {$role->name}\n";
    echo "Permisos: " . $role->permissions->pluck('name')->implode(', ') . "\n";
    echo "---\n";
}
