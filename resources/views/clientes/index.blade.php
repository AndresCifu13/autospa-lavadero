@extends('layouts.app')
@section('titulo', 'Clientes')

@section('contenido')
<x-encabezado titulo="Clientes" icono="bi-people" subtitulo="{{ $clientes->total() }} clientes registrados">
    <a href="{{ route('vehiculos.index') }}" class="btn btn-blanco"><i class="bi bi-car-front me-1"></i> Ver vehículos</a>
    <a href="{{ route('clientes.create') }}" class="btn btn-principal"><i class="bi bi-person-plus me-1"></i> Nuevo cliente</a>
</x-encabezado>

<form class="mb-3" method="GET">
    <div class="input-group">
        <span class="input-group-text bg-white" style="border-radius:12px 0 0 12px"><i class="bi bi-search"></i></span>
        <input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por nombre, documento, teléfono o placa">
        <button class="btn btn-suave" style="border-radius:0 12px 12px 0">Buscar</button>
    </div>
</form>

<div class="tarjeta overflow-hidden">
    @if ($clientes->isEmpty())
        <x-vacio icono="bi-people" texto="No se encontraron clientes." />
    @else
        <div class="table-responsive">
            <table class="table tabla">
                <thead><tr><th>Cliente</th><th class="d-none d-md-table-cell">Contacto</th><th class="text-center">Vehículos</th><th class="text-center d-none d-sm-table-cell">Visitas</th><th class="d-none d-sm-table-cell">Fidelización</th><th></th></tr></thead>
                <tbody>
                @foreach ($clientes as $cliente)
                    <tr>
                        <td>
                            <a href="{{ route('clientes.show', $cliente) }}" class="fw-bold text-reset">{{ $cliente->nombre }}</a>
                            <div class="small text-suave">{{ $cliente->documento ?? 'Sin documento' }}</div>
                        </td>
                        <td class="small d-none d-md-table-cell">{{ $cliente->telefono }}<div class="text-suave">{{ $cliente->email }}</div></td>
                        <td class="text-center">{{ $cliente->vehiculos_count }}</td>
                        <td class="text-center d-none d-sm-table-cell">{{ $cliente->lavados_count }}</td>
                        <td class="d-none d-sm-table-cell">
                            <span class="etiqueta pastel-verde">{{ $cliente->lavadas_acumuladas }}/{{ \App\Models\Ajuste::lavadasParaGratis() }}</span>
                            @if ($cliente->lavadas_gratis)<span class="etiqueta pastel-lila">🎁 {{ $cliente->lavadas_gratis }}</span>@endif
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-suave"><i class="bi bi-eye"></i></a>
                            <span class="d-none d-md-inline">
                                <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-blanco"><i class="bi bi-pencil"></i></a>
                                @can('delete', $cliente)
                                    <x-boton-eliminar :ruta="route('clientes.destroy', $cliente)" mensaje="Se eliminará a {{ $cliente->nombre }} y sus vehículos." />
                                @endcan
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $clientes->links() }}</div>
@endsection
