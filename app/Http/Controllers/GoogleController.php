<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

   public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->getId())->first(); 
            if ($user) {
                Auth::login($user);
                return redirect()->intended('dashboard');
            } else {
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    $existingUser->update([
                        'google_id' => $googleUser->getId(), 
                    ]);
                    Auth::login($existingUser);
                } else {
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
            return redirect()->route('login')->with('error', 'Hubo un problema al iniciar sesión con Google.');
        }
    }
}