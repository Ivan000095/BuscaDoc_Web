<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    // 1. Redirigir al usuario a la pantalla de Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Google nos devuelve al usuario aquí
    public function handleGoogleCallback()
    {
        try {
            // Obtenemos los datos de Google
            $googleUser = Socialite::driver('google')->user();

            // Buscamos si ya existe este usuario por su ID de Google
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Si existe, iniciamos sesión
                Auth::login($user);
                return redirect()->intended('dashboard'); // O '/home'
            } else {
                // Si NO existe, verificamos si existe por email
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // Si el correo ya existía, le vinculamos la cuenta de Google
                    $existingUser->update([
                        'google_id' => $googleUser->id,
                        // Opcional: actualizar foto si quieres
                        // 'foto' => $googleUser->avatar
                    ]);
                    Auth::login($existingUser);
                } else {
                    // Si es totalmente nuevo, lo creamos
                    $newUser = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'role' => 'paciente',
                        'password' => Hash::make('password_dummy_123'),
                        'foto' => $googleUser->getAvatar()
                    ]);
                    Auth::login($newUser);
                }
                
                return redirect()->intended('dashboard');
            }

        } catch (\Exception $e) {
            // Si algo falla, redirigir al login con error
            return redirect()->route('login')->with('error', 'Hubo un problema con Google Login');
        }
    }
}