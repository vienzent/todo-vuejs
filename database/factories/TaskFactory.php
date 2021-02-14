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
        static $order = 0;
        return [
            'description' => substr($this->faker->sentence(5), 0, 100),
            'is_completed' => 0,
            'order' => ++$order
        ];
    }
}
