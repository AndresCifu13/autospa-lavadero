@extends('layouts.app')
@section('titulo', 'Vehículos del día')

@section('contenido')
<x-encabezado titulo="Vehículos del día" icono="bi-car-front" subtitulo="{{ ucfirst($fecha->translatedFormat('l d \d\e F \d\e Y')) }}">
    <a href="{{ route('lavados.create') }}" class="btn btn-rosa"><i class="bi bi-plus-lg me-1"></i> Registrar vehículo</a>
</x-encabezado>

{{-- Filtros --}}
<form class="tarjeta tarjeta-cuerpo mb-3 row g-2 align-items-end mx-0" method="GET">
    <div class="col-6 col-md-3">
        <label class="form-label small" for="fecha">Fecha</label>
        <input type="date" id="fecha" name="fecha" value="{{ $fecha->toDateString() }}" max="{{ today()->toDateString() }}" class="form-control">
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small" for="estado">Estado</label>
        <select id="estado" name="estado" class="form-select">
            <option value="">Todos</option>
            @foreach (\App\Enums\EstadoLavado::opciones() as $valor => $texto)
                <option value="{{ $valor }}" @selected(request('estado') === $valor)>{{ $texto }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <label class="form-label small" for="pagado">Pago</label>
        <select id="pagado" name="pagado" class="form-select">
            <option value="">Todos</option>
            <option value="1" @selected(request('pagado') === '1')>Pagados</option>
            <option value="0" @selected(request('pagado') === '0')>Sin pagar</option>
        </select>
    </div>
    <div class="col-6 col-md-3 d-flex gap-2">
        <button class="btn btn-suave flex-grow-1"><i class="bi bi-funnel"></i> Filtrar</button>
        <a href="{{ route('lavados.index') }}" class="btn btn-blanco" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
    </div>
</form>

<div class="d-flex flex-wrap gap-2 mb-3">
    <span class="etiqueta pastel-azul">{{ $lavados->count() }} vehículos</span>
    <span class="etiqueta pastel-verde">Cobrado: {{ dinero($totalCobrado) }}</span>
    <span class="etiqueta pastel-amarillo">Por cobrar: {{ dinero($totalPorCobrar) }}</span>
</div>

<div class="tarjeta overflow-hidden">
    @if ($lavados->isEmpty())
        <x-vacio icono="bi-car-front" texto="No hay vehículos registrados con estos filtros." />
    @else
        {{-- Tabla en computador --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table tabla">
                <thead>
                    <tr>
                        <th>Hora</th><th>Placa</th><th>Cliente</th><th>Servicios</th><th>Empleado</th>
                        <th>Estado</th><th class="text-end">Total</th><th>Pago</th><th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($lavados as $lavado)
                    <tr>
                        <td class="small text-suave">{{ $lavado->created_at->format('h:i a') }}</td>
                        <td>
                            <span class="placa">{{ $lavado->vehiculo->placa }}</span>
                            <div class="small text-suave"><i class="bi {{ $lavado->vehiculo->tipo->icono() }}"></i> {{ $lavado->vehiculo->tipo->etiqueta() }}</div>
                        </td>
                        <td class="small">{{ $lavado->cliente->nombre }}</td>
                        <td class="small" style="max-width:200px">{{ $lavado->servicios->pluck('nombre')->implode(', ') }}</td>
                        <td class="small">{{ $lavado->empleado?->name ?? '—' }}</td>
                        <td><x-estado :estado="$lavado->estado" /></td>
                        <td class="text-end fw-bold">
                            {{ dinero($lavado->total) }}
                            @if ($lavado->es_gratis)<div><span class="etiqueta pastel-lila">Gratis</span></div>@endif
                        </td>
                        <td>
                            @if ($lavado->pagado)
                                <span class="etiqueta pastel-verde"><i class="bi bi-check2"></i> Pagado</span>
                            @else
                                <span class="etiqueta pastel-rosa">Pendiente</span>
                            @endif
                        </td>
                        <td class="text-end text-nowrap">
                            @can('avanzar', $lavado)
                                @if ($lavado->puedeAvanzar())
                                    <form method="POST" action="{{ route('lavados.avanzar', $lavado) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-verde" title="Pasar a {{ $lavado->estado->siguiente()->etiqueta() }}"><i class="bi bi-arrow-right"></i></button>
                                    </form>
                                @endif
                            @endcan
                            <a href="{{ route('lavados.show', $lavado) }}" class="btn btn-sm btn-suave" title="Ver detalle"><i class="bi bi-eye"></i></a>
                            @can('delete', $lavado)
                                <x-boton-eliminar :ruta="route('lavados.destroy', $lavado)" mensaje="Se eliminará el registro de la placa {{ $lavado->vehiculo->placa }}." />
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Lista en celular --}}
        <div class="d-md-none">
            @foreach ($lavados as $lavado)
                <a href="{{ route('lavados.show', $lavado) }}" class="item-lista d-block text-decoration-none text-reset">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="placa">{{ $lavado->vehiculo->placa }}</span>
                        <x-estado :estado="$lavado->estado" />
                    </div>
                    <div class="small text-suave">{{ $lavado->created_at->format('h:i a') }} · {{ $lavado->cliente->nombre }}</div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="small text-truncate me-2">{{ $lavado->servicios->pluck('nombre')->implode(', ') }}</span>
                        <span class="fw-bold text-nowrap">{{ dinero($lavado->total) }}
                            <i class="bi {{ $lavado->pagado ? 'bi-check-circle-fill text-success' : 'bi-clock text-danger' }}"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
