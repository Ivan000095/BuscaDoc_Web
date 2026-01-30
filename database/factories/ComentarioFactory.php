<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComentarioFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_autor' => User::factory(),
            'id_destinatario' => User::factory(),
            'tipo' => fake()->randomElement(["resena","pregunta"]),
            'calificacion' => fake()->numberBetween(-10000, 10000),
            'contenido' => fake()->text(),
            'user_id' => User::factory(),
        ];
    }
}
