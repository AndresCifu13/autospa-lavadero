@extends('layouts.app')
@section('titulo', 'Reporte mensual')

@section('contenido')
<x-encabezado titulo="Reporte mensual" icono="bi-calendar3" :subtitulo="ucfirst($mes->translatedFormat('F \d\e Y'))">
    <a href="{{ route('reportes.diario') }}" class="btn btn-blanco no-imprimir"><i class="bi bi-calendar-day me-1"></i> Ver diario</a>
    <button onclick="window.print()" class="btn btn-blanco no-imprimir"><i class="bi bi-printer"></i></button>
</x-encabezado>

<form class="d-flex gap-2 mb-3 no-imprimir" method="GET" style="max-width:360px">
    <input type="month" name="mes" value="{{ $mes->format('Y-m') }}" max="{{ now()->format('Y-m') }}" class="form-control">
    <button class="btn btn-suave">Ver</button>
</form>

@include('reportes._enviar', ['tipo' => 'mensual', 'valorFecha' => $mes->format('Y-m')])
@include('reportes._resumen')

{{-- Día por día --}}
<div class="tarjeta tarjeta-cuerpo mt-3">
    <h2 class="tarjeta-titulo"><i class="bi bi-graph-up"></i> Ingresos día por día</h2>
    @php($maximo = max(collect($reporte->porDia)->max('ingresos'), 1))
    <div class="grafica" style="height:180px;gap:3px">
        @foreach ($reporte->porDia as $dia => $valores)
            <div class="barra-col" title="{{ \Illuminate\Support\Carbon::parse($dia)->format('d/m') }}: {{ dinero($valores['ingresos']) }} (gastos {{ dinero($valores['gastos']) }})">
                <div class="barra" style="height: {{ max(2, round($valores['ingresos'] * 100 / $maximo)) }}%"></div>
                <small style="font-size:.6rem">{{ \Illuminate\Support\Carbon::parse($dia)->format('d') }}</small>
            </div>
        @endforeach
    </div>
</div>
@endsection
