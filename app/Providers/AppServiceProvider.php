<?php

namespace App\Providers;

use App\Models\Ajuste;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paginación con el estilo de Bootstrap 5
        Paginator::useBootstrapFive();

        // Fechas en español: "lunes 19 de octubre"
        Carbon::setLocale(config('app.locale'));

        // Compartimos el nombre del negocio con todas las vistas ($nombreNegocio)
        View::composer('*', function ($view) {
            static $nombre = null; // se busca una sola vez por petición
            $nombre ??= $this->nombreNegocio();
            $view->with('nombreNegocio', $nombre);
        });
    }

    private function nombreNegocio(): string
    {
        try {
            return Ajuste::obtener(Ajuste::NOMBRE_NEGOCIO, config('app.name'));
        } catch (Throwable) {
            return config('app.name'); // si la base de datos aún no existe
        }
    }
}
