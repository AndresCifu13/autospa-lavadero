<?php

use App\Http\Controllers\AjusteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\LavadoController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de la aplicación
|--------------------------------------------------------------------------
| guest      = solo para quien NO ha iniciado sesión
| auth       = hay que iniciar sesión
| rol:admin  = solo administradores (ver app/Http/Middleware/VerificarRol.php)
*/

// ---------- Inicio de sesión ----------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/perfil', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // ---------- Admin y empleados ----------

    // Vehículos del día / lavados
    Route::get('/lavados/buscar-placa', [LavadoController::class, 'buscarPlaca'])->name('lavados.buscar-placa');
    Route::patch('/lavados/{lavado}/avanzar', [LavadoController::class, 'avanzar'])->name('lavados.avanzar');
    Route::patch('/lavados/{lavado}/pagar', [LavadoController::class, 'pagar'])->name('lavados.pagar');
    Route::resource('lavados', LavadoController::class)->except(['edit', 'update']);

    Route::resource('clientes', ClienteController::class);
    Route::resource('vehiculos', VehiculoController::class)->except(['create', 'show']);

    // Tienda
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::resource('ventas', VentaController::class)->except(['edit', 'update']);

    // ---------- Solo administrador ----------
    Route::middleware('rol:admin')->group(function () {
        Route::resource('trabajadores', TrabajadorController::class)
            ->parameters(['trabajadores' => 'trabajador'])
            ->except('show');

        Route::resource('servicios', ServicioController::class)->except('show');
        Route::resource('gastos', GastoController::class)->except('show');
        Route::resource('productos', ProductoController::class)->except(['index', 'show']);

        Route::get('/reportes/diario', [ReporteController::class, 'diario'])->name('reportes.diario');
        Route::get('/reportes/mensual', [ReporteController::class, 'mensual'])->name('reportes.mensual');
        Route::post('/reportes/enviar', [ReporteController::class, 'enviar'])->name('reportes.enviar');

        Route::get('/ajustes', [AjusteController::class, 'edit'])->name('ajustes.edit');
        Route::put('/ajustes', [AjusteController::class, 'update'])->name('ajustes.update');
    });
});
