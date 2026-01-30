<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MensajeFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_remitente' => User::factory(),
            'id_destinatario' => User::factory(),
            'contenido' => fake()->text(),
            'leido' => fake()->boolean(),
            'user_id' => User::factory(),
        ];
    }
}
