<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pendiente', 'completado', 'en_proceso']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            // user_id, taskable_type, taskable_id will be set by the seeder or explicitly
        ];
    }
}
