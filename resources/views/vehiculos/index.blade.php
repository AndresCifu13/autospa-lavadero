@extends('layouts.app')
@section('titulo', 'Vehículos')

@section('contenido')
<x-encabezado titulo="Vehículos" icono="bi-car-front" subtitulo="Todos los vehículos registrados" />

<form class="tarjeta tarjeta-cuerpo mb-3 row g-2 mx-0" method="GET">
    <div class="col-md-6"><input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Placa o marca"></div>
    <div class="col-8 col-md-4">
        <select name="tipo" class="form-select">
            <option value="">Todos los tipos</option>
            @foreach ($tipos as $valor => $texto)<option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $texto }}</option>@endforeach
        </select>
    </div>
    <div class="col-4 col-md-2"><button class="btn btn-suave w-100"><i class="bi bi-search"></i></button></div>
</form>

<div class="tarjeta overflow-hidden">
    @if ($vehiculos->isEmpty())
        <x-vacio icono="bi-car-front" texto="No se encontraron vehículos." />
    @else
        <div class="table-responsive">
            <table class="table tabla">
                <thead><tr><th>Placa</th><th>Tipo</th><th class="d-none d-md-table-cell">Descripción</th><th>Dueño</th><th class="text-center">Lavados</th><th></th></tr></thead>
                <tbody>
                @foreach ($vehiculos as $vehiculo)
                    <tr>
                        <td><span class="placa">{{ $vehiculo->placa }}</span></td>
                        <td class="small"><i class="bi {{ $vehiculo->tipo->icono() }}"></i> {{ $vehiculo->tipo->etiqueta() }}</td>
                        <td class="small d-none d-md-table-cell">{{ $vehiculo->descripcion() ?: '—' }}</td>
                        <td class="small"><a href="{{ route('clientes.show', $vehiculo->cliente_id) }}">{{ $vehiculo->cliente?->nombre }}</a></td>
                        <td class="text-center">{{ $vehiculo->lavados_count }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('lavados.create', ['placa' => $vehiculo->placa]) }}" class="btn btn-sm btn-rosa" title="Registrar lavado"><i class="bi bi-droplet"></i></a>
                            <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-sm btn-blanco"><i class="bi bi-pencil"></i></a>
                            @can('delete', $vehiculo)
                                <x-boton-eliminar :ruta="route('vehiculos.destroy', $vehiculo)" mensaje="Se eliminará el vehículo {{ $vehiculo->placa }}." />
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $vehiculos->links() }}</div>
@endsection
