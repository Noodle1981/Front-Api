<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$emails = [
    'analista1@demo.com',
    'analista2@demo.com',
    'analista3@demo.com',
    'analista4@demo.com',
    'analista5@demo.com',
];

$count = User::whereIn('email', $emails)->delete();

echo "Se eliminaron {$count} usuarios de demostración (analistaX@demo.com).\n";
