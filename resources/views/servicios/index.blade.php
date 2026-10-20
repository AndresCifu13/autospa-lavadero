@extends('layouts.app')
@section('titulo', 'Servicios y precios')

@section('contenido')
<x-encabezado titulo="Servicios y precios" icono="bi-stars" subtitulo="El precio cambia según el tipo de vehículo">
    <a href="{{ route('servicios.create') }}" class="btn btn-principal"><i class="bi bi-plus-lg me-1"></i> Nuevo servicio</a>
</x-encabezado>

<div class="tarjeta overflow-hidden">
    @if ($servicios->isEmpty())
        <x-vacio icono="bi-stars" texto="Aún no hay servicios." />
    @else
        <div class="table-responsive">
            <table class="table tabla">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th class="text-end"><i class="bi bi-car-front"></i> Auto</th>
                        <th class="text-end"><i class="bi bi-truck-front"></i> Camioneta</th>
                        <th class="text-end"><i class="bi bi-bicycle"></i> Moto</th>
                        <th class="text-center d-none d-md-table-cell">Duración</th>
                        <th class="text-center d-none d-md-table-cell">Veces usado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($servicios as $servicio)
                    <tr @class(['opacity-50' => ! $servicio->activo])>
                        <td>
                            <div class="fw-bold">{{ $servicio->nombre }} @unless ($servicio->activo)<span class="etiqueta pastel-gris">Inactivo</span>@endunless</div>
                            <div class="small text-suave">{{ $servicio->descripcion }}</div>
                        </td>
                        <td class="text-end">{{ $servicio->precio_auto > 0 ? dinero($servicio->precio_auto) : '—' }}</td>
                        <td class="text-end">{{ $servicio->precio_camioneta > 0 ? dinero($servicio->precio_camioneta) : '—' }}</td>
                        <td class="text-end">{{ $servicio->precio_moto > 0 ? dinero($servicio->precio_moto) : '—' }}</td>
                        <td class="text-center small d-none d-md-table-cell">{{ $servicio->duracion_minutos }} min</td>
                        <td class="text-center d-none d-md-table-cell">{{ $servicio->lavados_count }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('servicios.edit', $servicio) }}" class="btn btn-sm btn-suave"><i class="bi bi-pencil"></i></a>
                            <x-boton-eliminar :ruta="route('servicios.destroy', $servicio)" mensaje="Si el servicio ya se usó, se desactivará en vez de borrarse." />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<p class="small text-suave mt-2"><i class="bi bi-info-circle"></i> Precio en $0 = el servicio no aplica para ese tipo de vehículo.</p>
@endsection
