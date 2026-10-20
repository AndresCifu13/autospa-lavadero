@extends('layouts.app')
@section('titulo', 'Ventas de la tienda')

@section('contenido')
<x-encabezado titulo="Ventas de la tienda" icono="bi-receipt" subtitulo="{{ ucfirst($fecha->translatedFormat('l d \d\e F')) }}">
    <a href="{{ route('ventas.create') }}" class="btn btn-rosa"><i class="bi bi-cart-plus me-1"></i> Nueva venta</a>
</x-encabezado>

<form class="d-flex gap-2 mb-3" method="GET" style="max-width:360px">
    <input type="date" name="fecha" value="{{ $fecha->toDateString() }}" max="{{ today()->toDateString() }}" class="form-control">
    <button class="btn btn-suave"><i class="bi bi-calendar-check"></i></button>
</form>

<div class="d-flex gap-2 mb-3">
    <span class="etiqueta pastel-azul">{{ $ventas->count() }} ventas</span>
    <span class="etiqueta pastel-verde">Total: {{ dinero($total) }}</span>
</div>

<div class="tarjeta overflow-hidden">
    @forelse ($ventas as $venta)
        <a href="{{ route('ventas.show', $venta) }}" class="item-lista d-flex justify-content-between align-items-center text-decoration-none text-reset">
            <div class="min-w-0">
                <div class="fw-bold">{{ $venta->codigo() }} <span class="small text-suave fw-normal">· {{ $venta->created_at->format('h:i a') }}</span></div>
                <div class="small text-truncate">{{ $venta->detalles->map(fn ($d) => $d->cantidad.'× '.$d->producto->nombre)->implode(', ') }}</div>
                <div class="small text-suave">{{ $venta->cliente?->nombre ?? 'Cliente de mostrador' }} · vendió {{ $venta->vendedor->name }}</div>
            </div>
            <div class="text-end ms-2">
                <div class="fw-black">{{ dinero($venta->total) }}</div>
                <span class="etiqueta pastel-lila">{{ $venta->metodo_pago->etiqueta() }}</span>
            </div>
        </a>
    @empty
        <x-vacio icono="bi-receipt" texto="No hay ventas este día.">
            <a href="{{ route('ventas.create') }}" class="btn btn-suave btn-sm">Hacer una venta</a>
        </x-vacio>
    @endforelse
</div>
@endsection
