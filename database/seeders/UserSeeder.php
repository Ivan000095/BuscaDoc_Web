<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CREAR TU CUENTA DE ADMINISTRADOR (La Única)
        User::create([
            'name' => 'Ivan Lanestosa',
            'email' => 'ivanlanestosa9@gmail.com', // Este será tu correo para entrar
            'password' => Hash::make('Bere2006'), // Tu contraseña
            'role' => 'admin', // <--- Aquí es donde se define el poder
            'estado' => true,
        ]);

        // 2. (Opcional) Crear usuarios de relleno para pruebas
        // User::factory(10)->create();
    }
}
