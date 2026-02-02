<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // <--- AGREGA ESTA LÍNEA OBLIGATORIAMENTE

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home'; // O '/dashboard', a donde quieras que vayan

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // --- AGREGA ESTA FUNCIÓN AL FINAL ---
    /**
     * El usuario ha sido autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Redirige a la ruta deseada CON el mensaje de la píldora
        return redirect()->intended($this->redirectPath())
            ->with('success', '¡Bienvenido de nuevo, ' . $user->name . '!');
    }
}