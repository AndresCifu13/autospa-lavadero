@extends('layouts.app')
@section('titulo', 'Reporte semanal')

@section('contenido')
<x-encabezado titulo="Reporte semanal" icono="bi-calendar-week"
    :subtitulo="'Del '.$reporte->desde->translatedFormat('d \d\e F').' al '.$reporte->hasta->translatedFormat('d \d\e F \d\e Y')">
    <button onclick="window.print()" class="btn btn-blanco no-imprimir"><i class="bi bi-printer"></i></button>
</x-encabezado>

@include('reportes._pestanas')

<div class="d-flex flex-wrap gap-2 mb-3 no-imprimir align-items-center">
    <a href="{{ route('reportes.semanal', ['semana' => $semana->copy()->subWeek()->toDateString()]) }}" class="btn btn-blanco" title="Semana anterior"><i class="bi bi-chevron-left"></i></a>
    <form class="d-flex gap-2" method="GET" style="max-width:300px">
        <input type="date" name="semana" value="{{ $semana->toDateString() }}" max="{{ today()->toDateString() }}" class="form-control" title="Elige cualquier día de la semana" onchange="this.form.submit()">
    </form>
    @if ($semana->copy()->addWeek()->lte(today()))
        <a href="{{ route('reportes.semanal', ['semana' => $semana->copy()->addWeek()->toDateString()]) }}" class="btn btn-blanco" title="Semana siguiente"><i class="bi bi-chevron-right"></i></a>
    @endif
</div>

@include('reportes._enviar', ['tipo' => 'semanal', 'valorFecha' => $semana->toDateString()])
@include('reportes._resumen')

{{-- Día por día --}}
<div class="tarjeta tarjeta-cuerpo mt-3">
    <h2 class="tarjeta-titulo"><i class="bi bi-graph-up"></i> Ingresos día por día</h2>
    @php($maximo = max(collect($reporte->porDia)->max('ingresos'), 1))
    <div class="grafica" style="height:180px;gap:8px">
        @foreach ($reporte->porDia as $dia => $valores)
            @php($fechaDia = \Illuminate\Support\Carbon::parse($dia))
            <div class="barra-col" title="{{ $fechaDia->translatedFormat('l d/m') }}: {{ dinero($valores['ingresos']) }} (gastos {{ dinero($valores['gastos']) }})">
                <div class="barra" style="height: {{ max(2, round($valores['ingresos'] * 100 / $maximo)) }}%"></div>
                <small style="font-size:.7rem">{{ ucfirst($fechaDia->translatedFormat('D d')) }}</small>
            </div>
        @endforeach
    </div>
</div>
@endsection
