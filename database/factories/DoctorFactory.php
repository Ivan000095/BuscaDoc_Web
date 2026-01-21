<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'especialidad' => fake()->word(),
            'name' => fake()->name(),
            'descripcion' => fake()->text(),
            'fecha' => fake()->date(),
            'image' => fake()->word(),
            'telefono' => fake()->word(),
            'idioma' => fake()->word(),
            'cedula' => fake()->word(),
            'direccion' => fake()->word(),
            'costos' => fake()->randomFloat(2, 0, 999999.99),
            'horarioentrada' => fake()->time(),
            'horariosalida' => fake()->time(),
        ];
    }
}
