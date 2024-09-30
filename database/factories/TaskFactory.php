<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\City;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status_id' => Status::all()->random()->id,
            'category_id' => Category::all()->random()->id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(1),
            'city_id' => City::all()->random()->id,
        ];
    }
}
