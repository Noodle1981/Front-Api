<?php
// database/factories/ClientFactory.php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ClientFactory extends Factory
{
    public function definition(): array
    {
        $companyName = fake()->company;

        return [
            // Datos de identificación básicos
            'fantasy_name' => $companyName,
            'company' => $companyName . ' S.R.L.',
            'cuit' => fake()->numerify('20########3'), // 11 digits
            'tax_condition' => fake()->randomElement(['Responsable Inscripto', 'Monotributo', 'Exento']),

            // Datos de contacto y fiscales
            'website' => 'https://www.' . fake()->domainName(),
            'email' => fake()->unique()->companyEmail,
            'phone' => fake()->phoneNumber,
            'address' => fake()->streetAddress(),
            'zip_code' => fake()->postcode(),
            'city' => fake()->city(),
            'state' => fake()->state(),

            // Datos de gestión y del sistema
            'internal_notes' => fake()->paragraph,
            'active' => true,
            'user_id' => User::factory(),
            'external_reference_id' => null,
        ];
    }
}