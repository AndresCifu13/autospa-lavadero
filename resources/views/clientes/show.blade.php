@extends('layouts.app')
@section('titulo', $cliente->nombre)

@section('contenido')
<x-encabezado :titulo="$cliente->nombre" icono="bi-person-heart" subtitulo="Cliente desde {{ $cliente->created_at->translatedFormat('F \d\e Y') }}">
    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-blanco"><i class="bi bi-pencil me-1"></i> Editar</a>
    @can('delete', $cliente)
        <x-boton-eliminar :ruta="route('clientes.destroy', $cliente)" texto="Eliminar" mensaje="Se eliminará a {{ $cliente->nombre }}." />
    @endcan
</x-encabezado>

<div class="row g-3">
    <div class="col-lg-4 d-flex flex-column gap-3">
        <div class="tarjeta tarjeta-cuerpo">
            <h2 class="tarjeta-titulo"><i class="bi bi-person-lines-fill"></i> Datos</h2>
            <dl class="row small mb-0">
                <dt class="col-5 text-suave">Documento</dt><dd class="col-7">{{ $cliente->documento ?? '—' }}</dd>
                <dt class="col-5 text-suave">Teléfono</dt><dd class="col-7">{{ $cliente->telefono ?? '—' }}</dd>
                <dt class="col-5 text-suave">Correo</dt><dd class="col-7 text-break">{{ $cliente->email ?? '—' }}</dd>
                <dt class="col-5 text-suave">Visitas</dt><dd class="col-7 mb-0">{{ $cliente->lavados()->count() }}</dd>
            </dl>
        </div>

        <div class="tarjeta tarjeta-cuerpo">
            <h2 class="tarjeta-titulo"><i class="bi bi-gift"></i> Tarjeta de fidelización</h2>
            <x-sellos :progreso="$progreso" />
            <p class="small text-suave mt-3 mb-0">
                @if ($progreso['gratis'] > 0)
                    <span class="etiqueta pastel-lila">🎁 {{ $progreso['gratis'] }} lavada(s) gratis disponible(s)</span>
                @else
                    Le faltan <b>{{ $progreso['faltan'] }}</b> lavadas para ganar una gratis.
                @endif
            </p>
        </div>
    </div>

    <div class="col-lg-8 d-flex flex-column gap-3">
        {{-- Vehículos --}}
        <div class="tarjeta">
            <div class="tarjeta-cuerpo pb-2 d-flex justify-content-between align-items-center">
                <h2 class="tarjeta-titulo mb-0"><i class="bi bi-car-front"></i> Vehículos</h2>
                <button class="btn btn-sm btn-suave" data-bs-toggle="collapse" data-bs-target="#nuevoVehiculo"><i class="bi bi-plus-lg"></i> Agregar</button>
            </div>

            <div class="collapse {{ $errors->any() ? 'show' : '' }}" id="nuevoVehiculo">
                <form method="POST" action="{{ route('vehiculos.store') }}" class="tarjeta-cuerpo pt-0 row g-2 align-items-end" novalidate>
                    @csrf
                    <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                    <x-campo class="col-6 col-md-3 mb-0" nombre="placa" etiqueta="Placa" requerido />
                    <x-seleccion class="col-6 col-md-3 mb-0" nombre="tipo" etiqueta="Tipo" :opciones="$tipos" />
                    <x-campo class="col-4 col-md-2 mb-0" nombre="marca" etiqueta="Marca" />
                    <x-campo class="col-4 col-md-2 mb-0" nombre="color" etiqueta="Color" />
                    <div class="col-4 col-md-2"><button class="btn btn-principal w-100">Guardar</button></div>
                </form>
            </div>

            @forelse ($cliente->vehiculos as $vehiculo)
                <div class="item-lista d-flex align-items-center gap-3">
                    <span class="avatar pastel-azul"><i class="bi {{ $vehiculo->tipo->icono() }}"></i></span>
                    <div class="flex-grow-1">
                        <span class="placa">{{ $vehiculo->placa }}</span>
                        <div class="small text-suave">{{ $vehiculo->tipo->etiqueta() }} {{ $vehiculo->descripcion() }}</div>
                    </div>
                    <a href="{{ route('lavados.create', ['placa' => $vehiculo->placa]) }}" class="btn btn-sm btn-rosa" title="Registrar lavado"><i class="bi bi-droplet"></i></a>
                    <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-sm btn-blanco"><i class="bi bi-pencil"></i></a>
                    @can('delete', $vehiculo)
                        <x-boton-eliminar :ruta="route('vehiculos.destroy', $vehiculo)" mensaje="Se eliminará el vehículo {{ $vehiculo->placa }}." />
                    @endcan
                </div>
            @empty
                <x-vacio icono="bi-car-front" texto="Este cliente no tiene vehículos." />
            @endforelse
        </div>

        {{-- Historial --}}
        <div class="tarjeta">
            <div class="tarjeta-cuerpo pb-2"><h2 class="tarjeta-titulo mb-0"><i class="bi bi-clock-history"></i> Últimas visitas</h2></div>
            @forelse ($cliente->lavados as $lavado)
                <a href="{{ route('lavados.show', $lavado) }}" class="item-lista d-flex justify-content-between align-items-center text-decoration-none text-reset">
                    <div>
                        <span class="placa">{{ $lavado->vehiculo->placa }}</span>
                        <span class="small text-suave ms-1">{{ $lavado->created_at->format('d/m/Y') }}</span>
                        <div class="small">{{ $lavado->servicios->pluck('nombre')->implode(', ') }}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold">{{ dinero($lavado->total) }}</div>
                        @if ($lavado->es_gratis)<span class="etiqueta pastel-lila">Gratis</span>@endif
                    </div>
                </a>
            @empty
                <x-vacio icono="bi-clock-history" texto="Aún no tiene visitas." />
            @endforelse
        </div>
    </div>
</div>
@endsection
