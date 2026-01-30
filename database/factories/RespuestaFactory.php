<?php

namespace Database\Factories;

use App\Models\Comentario;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RespuestaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'comentario_id' => Comentario::factory(),
            'id_respondedor' => User::factory(),
            'contenido' => fake()->text(),
            'user_id' => User::factory(),
        ];
    }
}
