<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Solo crear usuarios admin y user
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_admin' => true,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'is_admin' => false,
        ]);
    }
}