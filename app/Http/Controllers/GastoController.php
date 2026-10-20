<?php

namespace App\Http\Controllers;

use App\Enums\CategoriaGasto;
use App\Http\Requests\GastoRequest;
use App\Models\Gasto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Registro de gastos del día (HU-14). Solo administrador.
 */
class GastoController extends Controller
{
    public function index(Request $request): View
    {
        $desde = $request->date('desde') ?? now()->startOfMonth();
        $hasta = $request->date('hasta') ?? now();

        $consulta = Gasto::with('usuario')
            ->whereDate('fecha', '>=', $desde)
            ->whereDate('fecha', '<=', $hasta)
            ->when($request->categoria, fn ($q, $c) => $q->where('categoria', $c));

        return view('gastos.index', [
            'gastos' => (clone $consulta)->orderByDesc('fecha')->latest()->paginate(15)->withQueryString(),
            'total' => (clone $consulta)->sum('monto'),
            'desde' => $desde,
            'hasta' => $hasta,
            'categorias' => CategoriaGasto::opciones(),
        ]);
    }

    public function create(): View
    {
        return view('gastos.form', ['gasto' => new Gasto(['fecha' => today()]), 'categorias' => CategoriaGasto::opciones()]);
    }

    public function store(GastoRequest $request): RedirectResponse
    {
        Gasto::create([...$request->validated(), 'user_id' => $request->user()->id]);

        return redirect()->route('gastos.index')->with('exito', 'Gasto registrado.');
    }

    public function edit(Gasto $gasto): View
    {
        return view('gastos.form', ['gasto' => $gasto, 'categorias' => CategoriaGasto::opciones()]);
    }

    public function update(GastoRequest $request, Gasto $gasto): RedirectResponse
    {
        $gasto->update($request->validated());

        return redirect()->route('gastos.index')->with('exito', 'Gasto actualizado.');
    }

    public function destroy(Gasto $gasto): RedirectResponse
    {
        $gasto->delete();

        return back()->with('exito', 'Gasto eliminado.');
    }
}
