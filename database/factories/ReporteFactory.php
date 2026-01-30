<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReporteFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'id_usr_reporte' => User::factory(),
            'id_usr_reportado' => User::factory(),
            'razon' => fake()->text(),
            'user_id' => User::factory(),
        ];
    }
}
