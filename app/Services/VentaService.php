<?php

namespace App\Services;

use App\Enums\MetodoPago;
use App\Exceptions\NegocioException;
use App\Exceptions\StockInsuficienteException;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Notifications\StockBajo;
use Illuminate\Support\Facades\DB;

/**
 * Ventas de la tienda: descuenta el inventario y avisa si un producto se está acabando.
 */
class VentaService
{
    public function __construct(private NotificadorService $notificador) {}

    /**
     * @param  array<int, array{producto_id:int, cantidad:int}>  $items
     */
    public function registrar(array $items, MetodoPago $metodo, ?int $clienteId, User $vendedor): Venta
    {
        // Si el mismo producto viene dos veces, juntamos las cantidades
        $cantidades = [];
        foreach ($items as $item) {
            $id = (int) $item['producto_id'];
            $cantidades[$id] = ($cantidades[$id] ?? 0) + (int) $item['cantidad'];
        }

        if (empty($cantidades)) {
            throw new NegocioException('Agrega al menos un producto a la venta.');
        }

        $productosConStockBajo = [];

        $venta = DB::transaction(function () use ($cantidades, $metodo, $clienteId, $vendedor, &$productosConStockBajo) {
            // lockForUpdate: bloquea los productos mientras vendemos para que dos ventas
            // al mismo tiempo no dejen el stock en negativo
            $productos = Producto::activos()->whereIn('id', array_keys($cantidades))->lockForUpdate()->get()->keyBy('id');

            if ($productos->count() !== count($cantidades)) {
                throw new NegocioException('Uno de los productos ya no está disponible.');
            }

            $venta = Venta::create([
                'cliente_id' => $clienteId,
                'user_id' => $vendedor->id,
                'metodo_pago' => $metodo,
                'total' => 0,
            ]);

            $total = 0;
            foreach ($cantidades as $productoId => $cantidad) {
                $producto = $productos[$productoId];

                if ($producto->stock < $cantidad) {
                    throw StockInsuficienteException::para($producto, $cantidad);
                }

                $subtotal = $producto->precio * $cantidad;
                $venta->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ]);

                $producto->decrement('stock', $cantidad);
                $total += $subtotal;

                if ($producto->stockBajo()) {
                    $productosConStockBajo[] = $producto;
                }
            }

            $venta->update(['total' => $total]);

            return $venta;
        });

        // Avisamos fuera de la transacción (si el correo falla, la venta ya quedó guardada)
        foreach ($productosConStockBajo as $producto) {
            $this->notificador->enviar(User::admins()->activos()->get(), new StockBajo($producto));
        }

        return $venta->load('detalles.producto');
    }

    /** Anula una venta y devuelve los productos al inventario. */
    public function anular(Venta $venta): void
    {
        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                Producto::withTrashed()->whereKey($detalle->producto_id)->increment('stock', $detalle->cantidad);
            }

            $venta->delete();
        });
    }
}
