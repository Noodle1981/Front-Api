<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\DealStage;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Deal>
 */
class DealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'name' => 'Deal for ' . fake()->company,
        'value' => fake()->numberBetween(1000, 50000),
        'client_id' => Client::factory(),
        'user_id' => User::factory(),
        'deal_stage_id' => DealStage::factory(),
        'expected_close_date' => fake()->dateTimeBetween('+1 week', '+3 months'),
        'status' => 'open', 
    ];
}
}
