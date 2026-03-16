<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@buscadoc.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'estado' => true,
                'foto' => null,
                'f_nacimiento' => '1990-01-01',
                'latitud' => 16.9080,
                'longitud' => -92.0946,
            ]
        );

        $this->command->info('✅ Administrador creado o ya existe.');
    }
}