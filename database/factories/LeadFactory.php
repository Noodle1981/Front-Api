<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'name' => fake()->name,
        'company' => fake()->company,
        'email' => fake()->unique()->safeEmail,
        'phone' => fake()->phoneNumber,
        'source' => fake()->randomElement(['Web Form', 'Referral', 'Cold Call']),
        'status' => 'nuevo',
        'user_id' => User::factory(),
    ];
}
}
