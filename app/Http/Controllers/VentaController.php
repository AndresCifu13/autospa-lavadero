<?php

namespace App\Http\Controllers;

use App\Enums\MetodoPago;
use App\Http\Requests\VentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Ventas de la tienda del lavadero.
 */
class VentaController extends Controller
{
    public function __construct(private VentaService $ventas) {}

    public function index(Request $request): View
    {
        $fecha = $request->date('fecha') ?? now();
        $ventas = Venta::delDia($fecha)->with('cliente', 'vendedor', 'detalles.producto')->latest()->get();

        return view('ventas.index', ['ventas' => $ventas, 'fecha' => $fecha, 'total' => $ventas->sum('total')]);
    }

    /** Pantalla tipo "caja registradora". */
    public function create(): View
    {
        return view('ventas.create', [
            'productos' => Producto::activos()->where('stock', '>', 0)->orderBy('nombre')->get(),
            'clientes' => Cliente::orderBy('nombre')->get(['id', 'nombre', 'documento']),
            'metodos' => MetodoPago::opciones(),
        ]);
    }

    public function store(VentaRequest $request): RedirectResponse
    {
        $venta = $this->ventas->registrar(
            $request->validated('items'),
            MetodoPago::from($request->validated('metodo_pago')),
            $request->validated('cliente_id'),
            $request->user(),
        );

        return redirect()->route('ventas.show', $venta)->with('exito', 'Venta registrada por '.dinero($venta->total).'.');
    }

    public function show(Venta $venta): View
    {
        return view('ventas.show', ['venta' => $venta->load('detalles.producto', 'cliente', 'vendedor')]);
    }

    /** Anula la venta y devuelve los productos al inventario. Solo admin. */
    public function destroy(Venta $venta): RedirectResponse
    {
        $this->authorize('delete', $venta);
        $this->ventas->anular($venta);

        return redirect()->route('ventas.index')->with('exito', 'Venta anulada. Los productos volvieron al inventario.');
    }
}
