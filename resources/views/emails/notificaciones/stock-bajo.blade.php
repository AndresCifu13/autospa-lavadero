<x-mail::message>
# ⚠️ Un producto se está acabando

Hola **{{ $usuario->name }}**, este producto de la tienda llegó al stock mínimo:

<x-mail::panel>
📦 **{{ $producto->nombre }}**@if ($producto->categoria) · {{ $producto->categoria }}@endif
</x-mail::panel>

<x-mail::table>
| Dato | Valor |
|:-----|------:|
| Unidades disponibles | **{{ $producto->stock }}** |
| Mínimo configurado | {{ $producto->stock_minimo }} |
| Precio de venta | {{ dinero($producto->precio) }} |
</x-mail::table>

<span class="etiqueta etiqueta-rosa">Hay que reabastecer</span>

<x-mail::button :url="route('productos.edit', $producto)" color="error">
Actualizar inventario
</x-mail::button>

**{{ config('app.name') }}**
</x-mail::message>
