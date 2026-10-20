<?php

namespace App\Http\Controllers;

use App\Enums\TipoVehiculo;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Services\FidelizacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $clientes = Cliente::buscar($request->buscar)
            ->withCount('vehiculos', 'lavados')
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('clientes.form', ['cliente' => new Cliente]);
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        $cliente = Cliente::create($request->validated());

        return redirect()->route('clientes.show', $cliente)->with('exito', 'Cliente creado. Ahora puedes agregarle vehículos.');
    }

    public function show(Cliente $cliente, FidelizacionService $fidelizacion): View
    {
        $cliente->load(['vehiculos', 'lavados' => fn ($q) => $q->with('vehiculo', 'servicios')->latest()->take(15)]);

        return view('clientes.show', [
            'cliente' => $cliente,
            'progreso' => $fidelizacion->progreso($cliente),
            'tipos' => TipoVehiculo::opciones(),
        ]);
    }

    public function edit(Cliente $cliente): View
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $this->authorize('update', $cliente);
        $cliente->update($request->validated());

        return redirect()->route('clientes.show', $cliente)->with('exito', 'Datos del cliente actualizados.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $this->authorize('delete', $cliente);
        $cliente->delete();

        return redirect()->route('clientes.index')->with('exito', "Se eliminó el cliente {$cliente->nombre}.");
    }
}
