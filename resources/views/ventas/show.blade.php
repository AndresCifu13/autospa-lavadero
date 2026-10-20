@extends('layouts.app')
@section('titulo', 'Venta '.$venta->codigo())

@section('contenido')
<x-encabezado titulo="Venta {{ $venta->codigo() }}" icono="bi-receipt" subtitulo="{{ $venta->created_at->translatedFormat('d M Y, h:i a') }} · vendió {{ $venta->vendedor->name }}">
    <a href="{{ route('ventas.index') }}" class="btn btn-blanco no-imprimir"><i class="bi bi-arrow-left"></i> Volver</a>
    <button onclick="window.print()" class="btn btn-blanco no-imprimir"><i class="bi bi-printer"></i> Imprimir</button>
    @can('delete', $venta)
        <x-boton-eliminar :ruta="route('ventas.destroy', $venta)" texto="Anular" class="no-imprimir" mensaje="Se anulará la venta y los productos volverán al inventario." />
    @endcan
</x-encabezado>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="tarjeta tarjeta-cuerpo">
            <div class="d-flex justify-content-between mb-3 small">
                <span><span class="text-suave">Cliente:</span> <b>{{ $venta->cliente?->nombre ?? 'Cliente de mostrador' }}</b></span>
                <span class="etiqueta pastel-lila">{{ $venta->metodo_pago->etiqueta() }}</span>
            </div>
            <table class="table tabla mb-0">
                <thead><tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">Precio</th><th class="text-end">Subtotal</th></tr></thead>
                <tbody>
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td class="text-center">{{ $detalle->cantidad }}</td>
                        <td class="text-end">{{ dinero($detalle->precio_unitario) }}</td>
                        <td class="text-end">{{ dinero($detalle->subtotal) }}</td>
                    </tr>
                @endforeach
                <tr><td colspan="3" class="fw-black fs-5">Total</td><td class="text-end fw-black fs-5">{{ dinero($venta->total) }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
