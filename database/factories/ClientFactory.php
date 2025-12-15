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
            'name' => $companyName,
            'company' => $companyName . ' S.R.L.',
            'cuit' => '30-' . fake()->numerify('########') . '-' . fake()->randomDigit(),
            
            // Datos de contacto y fiscales
            'website' => 'https://www.' . fake()->domainName(),
            'email' => fake()->unique()->companyEmail,
            'phone' => fake()->phoneNumber,
            'fiscal_address_street' => fake()->streetAddress(),
            'fiscal_address_zip_code' => fake()->postcode(),
            'fiscal_address_city' => fake()->city(),
            'fiscal_address_state' => fake()->state(),
            'fiscal_address_country' => 'Argentina',

            // Datos de H&S
            'economic_activity' => fake()->randomElement(['Construcción', 'Industria Manufacturera', 'Servicios', 'Comercio']),
            'art_provider' => fake()->randomElement(['Provincia ART', 'La Segunda ART', 'Federación Patronal ART', 'Galeno ART']),
            'art_registration_date' => fake()->optional()->date(),

            // Datos de gestión y del sistema
            'notes' => fake()->paragraph,
            'active' => true,
            'user_id' => User::factory(),
            'hs_platform_empresa_id' => null, // El ID de la plataforma externa es nulo por defecto
        ];
    }
}