<?php
// database/factories/EstablishmentFactory.php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Establishment>
 */
class EstablishmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Siempre se asociará a un cliente existente o creará uno nuevo.
            'client_id' => Client::factory(),
            
            // Nombres realistas para los establecimientos.
            'name' => fake()->randomElement(['Planta de Producción', 'Oficinas Centrales', 'Depósito Logístico', 'Sucursal ' . fake()->city()]),
            
            // Datos de dirección completos.
            'address_street' => fake()->streetAddress(),
            'address_city' => fake()->city(),
            'address_zip_code' => fake()->postcode(),
            'address_state' => fake()->state(),
            'address_country' => 'Argentina',
            
            // Coordenadas geográficas.
            'latitude' => fake()->latitude(-54, -22), // Rango para Argentina
            'longitude' => fake()->longitude(-73, -53), // Rango para Argentina

            // Por defecto, un establecimiento está activo y no tiene notas.
            'active' => true,
            'notes' => fake()->optional()->sentence(),
            
            // El ID de la plataforma externa es nulo por defecto.
            'hs_platform_sede_id' => null,
        ];
    }
}