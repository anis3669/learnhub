<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'teacher_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'category' => 'Programming',
            'level' => 'Beginner',
            'is_published' => true,
            'duration_hours' => fake()->numberBetween(1, 20),
            'price' => 0,
        ];
    }
}
