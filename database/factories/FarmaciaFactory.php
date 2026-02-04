<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FarmaciaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nom_farmacia' => fake()->word(),
            'rfc' => fake()->word(),
            'telefono' => fake()->word(),
            'descripcion' => fake()->text(),
            'horario' => fake()->word(),
            'dias_op' => fake()->word(),
        ];
    }
}
