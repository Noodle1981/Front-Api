<?php
// database/factories/ContactFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;

class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            // Por defecto, un contacto es corporativo (no está asignado a un establecimiento).
            // Esto se puede sobreescribir en el seeder.
            'establishment_id' => null,
            
            'name' => fake()->name,
            'email' => fake()->unique()->safeEmail,
            'phone' => fake()->phoneNumber,
            'position' => fake()->jobTitle,
            'notes' => fake()->optional()->sentence(),
            'active' => true,
        ];
    }
}