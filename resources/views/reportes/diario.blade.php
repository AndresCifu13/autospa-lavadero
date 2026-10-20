@extends('layouts.app')
@section('titulo', 'Reporte diario')

@section('contenido')
<x-encabezado titulo="Reporte diario" icono="bi-bar-chart-line" :subtitulo="ucfirst($fecha->translatedFormat('l d \d\e F \d\e Y'))">
    <a href="{{ route('reportes.mensual') }}" class="btn btn-blanco no-imprimir"><i class="bi bi-calendar3 me-1"></i> Ver mensual</a>
    <button onclick="window.print()" class="btn btn-blanco no-imprimir"><i class="bi bi-printer"></i></button>
</x-encabezado>

<form class="d-flex gap-2 mb-3 no-imprimir" method="GET" style="max-width:360px">
    <input type="date" name="fecha" value="{{ $fecha->toDateString() }}" max="{{ today()->toDateString() }}" class="form-control">
    <button class="btn btn-suave">Ver</button>
</form>

@include('reportes._enviar', ['tipo' => 'diario', 'valorFecha' => $fecha->toDateString()])
@include('reportes._resumen')
@endsection
