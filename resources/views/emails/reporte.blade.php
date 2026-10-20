<x-mail::message>
# {{ $reporte->titulo }}

Hola, este es el resumen del lavadero:

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
⚠️ Hay **{{ $reporte->lavadosPendientesDePago }}** lavados sin pagar que no se sumaron.
@endif

@if (count($reporte->porServicio))
**Servicios más pedidos:**
@foreach (array_slice($reporte->porServicio, 0, 5) as $fila)
- {{ $fila['nombre'] }}: {{ $fila['cantidad'] }} ({{ dinero($fila['total']) }})
@endforeach
@endif

<x-mail::button :url="route('reportes.diario')">
Ver reporte completo
</x-mail::button>

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
