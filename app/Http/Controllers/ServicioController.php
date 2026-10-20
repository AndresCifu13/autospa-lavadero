<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicioRequest;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Catálogo de servicios y precios por tipo de vehículo (HU-07). Solo administrador.
 */
class ServicioController extends Controller
{
    public function index(): View
    {
        return view('servicios.index', [
            'servicios' => Servicio::withCount('lavados')->orderByDesc('activo')->orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        return view('servicios.form', ['servicio' => new Servicio(['activo' => true, 'duracion_minutos' => 30])]);
    }

    public function store(ServicioRequest $request): RedirectResponse
    {
        Servicio::create($request->validated());

        return redirect()->route('servicios.index')->with('exito', 'Servicio creado.');
    }

    public function edit(Servicio $servicio): View
    {
        return view('servicios.form', compact('servicio'));
    }

    public function update(ServicioRequest $request, Servicio $servicio): RedirectResponse
    {
        $servicio->update($request->validated());

        return redirect()->route('servicios.index')->with('exito', 'Servicio actualizado. Los lavados anteriores conservan su precio.');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        // Si ya se usó en lavados no lo borramos (se perdería el historial): lo desactivamos
        if ($servicio->lavados()->exists()) {
            $servicio->update(['activo' => false]);

            return back()->with('exito', 'El servicio ya tiene lavados registrados, así que se desactivó en lugar de borrarse.');
        }

        $servicio->delete();

        return back()->with('exito', 'Servicio eliminado.');
    }
}
