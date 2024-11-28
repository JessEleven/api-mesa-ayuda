<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
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
        // Para entorno dev cada vez que se cambien el nombre de las rutas
        // desde el archivo api.php
        if (app()->isLocal() && file_exists(base_path('bootstrap/cache/routes.php'))) {
            // Limpiar la caché de rutas solo si está activada
            Artisan::call('route:clear');
        }
    }
}
