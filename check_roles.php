<?php

use Spatie\Permission\Models\Role;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = Role::all();
echo "Roles en la base de datos:\n";
foreach ($roles as $role) {
    echo "- " . $role->name . " (ID: " . $role->id . ")\n";
}
