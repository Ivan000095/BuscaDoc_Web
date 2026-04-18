<?php

namespace App\Providers;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzamos el idioma a Carbon globalmente
    Carbon::setLocale('es');
    
    // Opcional: Intentar setearlo a nivel sistema para PHP
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
    }
}
