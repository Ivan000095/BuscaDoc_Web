<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorEspecialidadFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tableName' => fake()->word(),
            'doctor_id' => Doctor::factory(),
            'especialidad_id' => Especialidad::factory(),
        ];
    }
}
