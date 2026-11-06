<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'student_number' => fake()->randomElement(['BSIT', 'BSCS']) . '-2025-' . fake()->unique()->numerify('####'),
        'first_name'     => fake()->firstName(),
        'last_name'      => fake()->lastName(),
        'company_id'     => Company::query()->inRandomOrder()->value('id'),
        'required_hours' => 486,
        'rendered_hours' => fake()->numberBetween(0, 486),
        // weighted so most students are on track
        'risk_level'     => fake()->randomElement([
            ...array_fill(0, 12, 'on_track'), 'medium', 'medium', 'high',
        ]),
    ];
}
}
