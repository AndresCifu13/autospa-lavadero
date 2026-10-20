<?php

use App\Http\Middleware\UsuarioActivo;
use App\Http\Middleware\VerificarRol;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Nombre corto para usar en las rutas: ->middleware('rol:admin')
        $middleware->alias(['rol' => VerificarRol::class]);

        // Revisa en cada petición que el usuario siga activo
        $middleware->web(append: [UsuarioActivo::class]);

        // A dónde mandar a quien no ha iniciado sesión / a quien ya inició
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Manejo de errores de base de datos: en lugar de la pantalla roja
        // mostramos un mensaje amable y guardamos el detalle en el log.
        $exceptions->render(function (QueryException $e, Request $request) {
            if (config('app.debug') || $request->expectsJson()) {
                return null; // en desarrollo dejamos ver el error completo
            }

            Log::error('Error de base de datos', ['sql' => $e->getSql(), 'error' => $e->getMessage()]);

            // Código 23000 = se intentó borrar algo que otros registros usan
            $mensaje = $e->getCode() === '23000'
                ? 'No se puede completar la acción porque hay otros registros relacionados.'
                : 'Hubo un problema con la base de datos. Inténtalo de nuevo.';

            return back()->withInput()->with('error', $mensaje);
        });

        // Registro que no existe (ej: /clientes/9999) => página 404 bonita
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if (! $request->expectsJson()) {
                return response()->view('errors.404', [], 404);
            }
        });
    })->create();
