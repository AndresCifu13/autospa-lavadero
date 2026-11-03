<x-mail::message>
# ¡Tu vehículo ya está listo! ✨

Hola **{{ $cliente->nombre }}**, terminamos de lavar tu vehículo y ya puedes pasar a recogerlo.

<x-mail::panel>
🚗 **{{ $vehiculo->tipo->etiqueta() }} {{ trim(($vehiculo->marca ?? '').' '.($vehiculo->modelo ?? '')) }}**<br>
Placa: **{{ $vehiculo->placa }}**@if ($vehiculo->color) · Color: {{ $vehiculo->color }}@endif
</x-mail::panel>

<x-mail::table>
| Servicio | Valor |
|:---------|------:|
@foreach ($lavado->servicios as $servicio)
| {{ $servicio->nombre }} | {{ dinero($servicio->pivot->precio ?? 0) }} |
@endforeach
@if ($lavado->descuento > 0)
| Descuento | − {{ dinero($lavado->descuento) }} |
@endif
| **Total** | **{{ $lavado->es_gratis ? 'GRATIS 🎁' : dinero($lavado->total) }}** |
</x-mail::table>

@if ($lavado->pagado || $lavado->es_gratis)
<span class="etiqueta etiqueta-verde">✔ Servicio pagado</span>
@else
<span class="etiqueta etiqueta-amarillo">Pendiente por pagar: {{ dinero($lavado->total) }}</span>
@endif

¡Te esperamos!<br>
**{{ config('app.name') }}**
</x-mail::message>
