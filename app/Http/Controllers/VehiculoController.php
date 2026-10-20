<?php

namespace App\Http\Controllers;

use App\Enums\TipoVehiculo;
use App\Http\Requests\VehiculoRequest;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiculoController extends Controller
{
    public function index(Request $request): View
    {
        $vehiculos = Vehiculo::with('cliente')
            ->withCount('lavados')
            ->when($request->buscar, fn ($q, $texto) => $q->where(fn ($q) => $q
                ->where('placa', 'like', '%'.Vehiculo::limpiarPlaca($texto).'%')
                ->orWhere('marca', 'like', "%{$texto}%")))
            ->when($request->tipo, fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->orderBy('placa')
            ->paginate(12)
            ->withQueryString();

        return view('vehiculos.index', ['vehiculos' => $vehiculos, 'tipos' => TipoVehiculo::opciones()]);
    }

    public function store(VehiculoRequest $request): RedirectResponse
    {
        $vehiculo = Vehiculo::create($request->validated());

        return redirect()->route('clientes.show', $vehiculo->cliente_id)->with('exito', "Vehículo {$vehiculo->placa} agregado.");
    }

    public function edit(Vehiculo $vehiculo): View
    {
        return view('vehiculos.edit', ['vehiculo' => $vehiculo, 'tipos' => TipoVehiculo::opciones()]);
    }

    public function update(VehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        $this->authorize('update', $vehiculo);
        $vehiculo->update($request->validated());

        return redirect()->route('clientes.show', $vehiculo->cliente_id)->with('exito', 'Vehículo actualizado.');
    }

    public function destroy(Vehiculo $vehiculo): RedirectResponse
    {
        $this->authorize('delete', $vehiculo);
        $vehiculo->delete();

        return back()->with('exito', "Se eliminó el vehículo {$vehiculo->placa}.");
    }
}
