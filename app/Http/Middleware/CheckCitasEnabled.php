<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCitasEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Verificar si el usuario está autenticado y es doctor
        // 2. Verificar si tiene habilitada la opción de citas
        if (auth()->check() && auth()->user()->role === 'doctor') {
            if (!auth()->user()->doctor->citas) {
                // Si no acepta citas, lo mandamos al dashboard con un mensaje
                return redirect()->route('home')->with('error', 'No tienes habilitada la recepción de citas.');
            }
        }

        return $next($request);
    }
}
