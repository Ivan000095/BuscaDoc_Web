<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitaFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'paciente_id' => Paciente::factory(),
            'fecha_hora' => fake()->dateTime(),
            'detalles' => fake()->text(),
            'estado' => fake()->randomElement(["pendiente","confirmada","cancelada","completada"]),
            'user_id' => User::factory(),
        ];
    }
}
