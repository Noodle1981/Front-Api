<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// Create Analista User
$email = 'analista@example.com';
$user = User::firstOrCreate(
    ['email' => $email],
    [
        'name' => 'Analista Contable',
        'password' => Hash::make('password'),
        'is_admin' => false,
    ]
);

// Assign Role
if (!Role::where('name', 'Analista')->exists()) {
    Role::create(['name' => 'Analista']);
}

$user->syncRoles(['Analista']);

echo "User Created/Updated:\n";
echo "Email: $email\n";
echo "Password: password\n";
echo "Role: " . $user->getRoleNames()->first() . "\n";
