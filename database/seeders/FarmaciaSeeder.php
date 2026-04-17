<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Farmacia; // Asegúrate de tener este modelo creado
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class FarmaciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Farmacia::truncate();
        User::where('role', 'farmacia')->delete();
        $faker = Faker::create('es_MX');
        $password = Hash::make('password123');

        $nombresFarmacias = [
            'Farmacia San Juan', 'Farmacia del Centro', 'Súper Farmacia Ocosingo', 
            'Farmacia La Salud', 'Farmacia Cristo Rey', 'Farmacia Guadalupe', 
            'Farmacia La Paz', 'Farmacia Nueva', 'Farmacia San Antonio', 'Farmacia Principal'
        ];

         $imagenesDisponibles = Storage::disk('public')->files('perfiles');

        for ($i = 0; $i < 10; $i++) {
            $fotoAleatoria = !empty($imagenesDisponibles) ? $faker->randomElement($imagenesDisponibles) : null;
        
            $nombreFarmacia = $nombresFarmacias[$i];
            $user = User::create([
                'name' => 'Admin ' . $nombreFarmacia,
                'email' => "contacto.farmacia" . ($i + 1) . "@buscadoc.com",
                'password' => $password,
                'role' => 'farmacia',
                'f_nacimiento' => $faker->date('Y-m-d', '1990-01-01'),
                'estado' => true,
                'latitud' => $faker->randomFloat(6, 16.8900, 16.9200),
                'longitud' => $faker->randomFloat(6, -92.1100, -92.0800),
                'foto' => $fotoAleatoria,
            ]);
            Farmacia::create([
                'user_id' => $user->id,
                'nom_farmacia' => $nombreFarmacia,
                'rfc' => strtoupper($faker->lexify('???') . $faker->numerify('######') . $faker->lexify('???')), 
                'telefono' => $faker->numerify('919#######'), 
                'descripcion' => "Farmacia de prueba generada automáticamente",
                'horario_entrada' => '08:00:00',
                'horario_salida' => '22:00:00',
            ]);
        }
    }
}