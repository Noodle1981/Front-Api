<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DealStage>
 */
class DealStageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true), // Ej: "Initial Contact"
            'order' => fake()->unique()->numberBetween(1, 10), // Un número de orden único
        ];
    }
}
