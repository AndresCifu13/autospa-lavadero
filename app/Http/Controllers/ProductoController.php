<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Inventario de la tienda. Todos lo pueden ver; solo el admin crea, edita y elimina.
 */
class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $productos = Producto::buscar($request->buscar)
            ->when($request->boolean('stock_bajo'), fn ($q) => $q->conStockBajo())
            ->orderByDesc('activo')->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        return view('productos.index', compact('productos'));
    }

    public function create(): View
    {
        return view('productos.form', ['producto' => new Producto(['activo' => true, 'stock_minimo' => 5]), 'categorias' => $this->categorias()]);
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        Producto::create($request->validated());

        return redirect()->route('productos.index')->with('exito', 'Producto agregado a la tienda.');
    }

    public function edit(Producto $producto): View
    {
        return view('productos.form', ['producto' => $producto, 'categorias' => $this->categorias()]);
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return redirect()->route('productos.index')->with('exito', 'Producto actualizado.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->delete();

        return back()->with('exito', "Se eliminó {$producto->nombre} de la tienda.");
    }

    /** Categorías que ya existen, para sugerirlas en el formulario. */
    private function categorias(): array
    {
        return Producto::whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria')->all();
    }
}
