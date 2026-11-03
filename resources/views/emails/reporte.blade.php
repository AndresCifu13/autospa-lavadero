<x-mail::message>
# 📊 {{ $reporte->titulo }}

Hola, este es el resumen de ventas del lavadero.

<x-mail::panel>
💰 **Total ingresos: {{ dinero($reporte->totalIngresos()) }}**<br>
@if ($reporte->utilidad() >= 0)
✅ Utilidad: **{{ dinero($reporte->utilidad()) }}**
@else
🔻 Pérdida: **{{ dinero(abs($reporte->utilidad())) }}**
@endif
</x-mail::panel>

## Resumen

<x-mail::table>
| Concepto | Valor |
|:--------------------|-------------:|
| Vehículos atendidos | {{ $reporte->cantidadLavados }} |
| Ingresos por lavados | {{ dinero($reporte->ingresosLavados) }} |
| Ventas en tienda ({{ $reporte->cantidadVentas }}) | {{ dinero($reporte->ingresosTienda) }} |
| **Total ingresos** | **{{ dinero($reporte->totalIngresos()) }}** |
| Gastos | {{ dinero($reporte->totalGastos) }} |
| **Utilidad** | **{{ dinero($reporte->utilidad()) }}** |
</x-mail::table>

@if ($reporte->lavadosPendientesDePago)
<span class="etiqueta etiqueta-amarillo">⚠️ {{ $reporte->lavadosPendientesDePago }} lavados sin pagar (no se sumaron)</span>
@endif

@if (count($reporte->porMetodoPago))
## Por método de pago

<x-mail::table>
| Método | Total |
|:-------|------:|
@foreach ($reporte->porMetodoPago as $metodo => $total)
| {{ $metodo }} | {{ dinero($total) }} |
@endforeach
</x-mail::table>
@endif

@if (count($reporte->porServicio))
## Servicios más pedidos

<x-mail::table>
| Servicio | Cantidad | Total |
|:---------|:--------:|------:|
@foreach (array_slice($reporte->porServicio, 0, 5) as $fila)
| {{ $fila['nombre'] }} | {{ $fila['cantidad'] }} | {{ dinero($fila['total']) }} |
@endforeach
</x-mail::table>
@endif

@if ($reporte->tipo === 'semanal' && count($reporte->porDia))
## Día por día

<x-mail::table>
| Día | Ingresos | Gastos |
|:----|---------:|-------:|
@foreach ($reporte->porDia as $dia => $valores)
| {{ ucfirst(\Illuminate\Support\Carbon::parse($dia)->translatedFormat('l d')) }} | {{ dinero($valores['ingresos']) }} | {{ dinero($valores['gastos']) }} |
@endforeach
</x-mail::table>
@endif

@if (count($reporte->porEmpleado))
## Empleados

<x-mail::table>
| Empleado | Lavados | Cobrado |
|:---------|:-------:|--------:|
@foreach (array_slice($reporte->porEmpleado, 0, 5) as $fila)
| {{ $fila['nombre'] }} | {{ $fila['cantidad'] }} | {{ dinero($fila['total']) }} |
@endforeach
</x-mail::table>
@endif

<x-mail::button :url="$reporte->ruta()">
Ver reporte completo
</x-mail::button>

Saludos,<br>
**{{ config('app.name') }}**
</x-mail::message>
