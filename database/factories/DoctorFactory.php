<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'cedula' => fake()->word(),
            'idiomas' => fake()->word(),
            'descripcion' => fake()->text(),
            'costo' => fake()->randomFloat(2, 0, 999999.99),
            'horario_entrada' => fake()->time(),
            'horario_salida' => fake()->time(),
        ];
    }
}
