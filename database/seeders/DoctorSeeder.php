<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        $password = Hash::make('password123');

        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => $faker->firstName() . ' ' . $faker->lastName(),
                'email' => "doctor{$i}@buscadoc.com",
                'password' => $password,
                'role' => 'doctor',
                'f_nacimiento' => $faker->date('Y-m-d', '1990-01-01'),
                
                'latitud' => $faker->randomFloat(6, 16.8900, 16.9200),
                'longitud' => $faker->randomFloat(6, -92.1100, -92.0800),
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'cedula' => 'CED-' . $faker->numerify('########'),
                'costo' => $faker->randomElement([250.00, 300.00, 350.00, 400.00, 500.00]),
                'horario_entrada' => '09:00:00',
                'horario_salida' => '18:00:00',
                // Mezclamos un poco los idiomas
                'idiomas' => $faker->randomElement(['Español', 'Español, Tzeltal', 'Español, Inglés']),
                'descripcion' => "Médico especialista de prueba generado por Seeder. Atiende con gran dedicación a sus pacientes.",
            ]);

            $especialidadIdAleatoria = $faker->numberBetween(1, 51);
            $doctor->especialidades()->sync([$especialidadIdAleatoria]);
        }
    }
}