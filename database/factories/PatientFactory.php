<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'date_born' => fake()->date('Y-m-d', '1998-08-01'),
            'gender' => fake()->randomElement(['Masculino', 'Femenino']),
            'address' => fake()->text(30),
            'phone' => fake()->numerify('503-###-###'),
            'email' => fake()->safeEmail()
        ];
    }
}
