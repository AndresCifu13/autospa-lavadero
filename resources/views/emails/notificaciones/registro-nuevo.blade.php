<x-mail::message>
# {{ $icono }} {{ $titulo }}

Se acaba de registrar esto en **{{ config('app.name') }}**:

<x-mail::table>
| Dato | Detalle |
|:-----|:--------|
@foreach ($datos as $etiqueta => $valor)
| {{ $etiqueta }} | {{ str_replace(['|', "\n"], ['/', ' '], (string) $valor) }} |
@endforeach
</x-mail::table>

<x-mail::panel>
🕒 {{ ucfirst($fecha->translatedFormat('l d \d\e F \d\e Y, h:i a')) }}@if ($registradoPor)<br>
👤 Registrado por: **{{ $registradoPor }}**@endif
</x-mail::panel>

@if ($url)
<x-mail::button :url="$url">
Ver en el sistema
</x-mail::button>
@endif

**{{ config('app.name') }}**
</x-mail::message>
