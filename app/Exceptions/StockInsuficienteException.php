<?php

namespace App\Exceptions;

use App\Models\Producto;

class StockInsuficienteException extends NegocioException
{
    public static function para(Producto $producto, int $pedido): self
    {
        return new self("No hay suficiente stock de \"{$producto->nombre}\". Pediste {$pedido} y solo quedan {$producto->stock}.");
    }
}
