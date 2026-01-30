<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'password' => fake()->password(),
            'role' => fake()->randomElement(["admin","doctor","paciente","farmacia"]),
            'foto' => fake()->word(),
            'f_nacimiento' => fake()->date(),
            'genero' => fake()->word(),
            'latitud' => fake()->randomFloat(8, 0, 99.99999999),
            'longitud' => fake()->randomFloat(8, 0, 999.99999999),
            'estado' => fake()->boolean(),
        ];
    }
}
