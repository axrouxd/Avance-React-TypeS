<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     * Typically used for redirection after authentication.
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            // Rutas API (api.php)
            Route::middleware('api')
                ->prefix('api') // Todas las rutas tendrán el prefijo /api
                ->group(base_path('routes/api.php'));

            // Rutas Web (web.php)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     * Esto limita las peticiones a la API para prevenir abuso.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60) // 60 peticiones por minuto
                ->by($request->user()?->id ?: $request->ip()); // Límite por usuario o IP
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Puedes registrar bindings de servicios aquí si es necesario
    }
}