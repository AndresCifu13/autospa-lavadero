@extends('layouts.app')
@section('titulo', $servicio->exists ? 'Editar servicio' : 'Nuevo servicio')

@section('contenido')
<x-encabezado :titulo="$servicio->exists ? 'Editar servicio' : 'Nuevo servicio'" icono="bi-stars" />

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ $servicio->exists ? route('servicios.update', $servicio) : route('servicios.store') }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf
            @if ($servicio->exists) @method('PUT') @endif

            <x-campo nombre="nombre" etiqueta="Nombre del servicio" :valor="$servicio->nombre" requerido />
            <div class="mb-3">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $servicio->descripcion) }}</textarea>
            </div>

            <p class="form-label mb-2">Precio por tipo de vehículo <span class="small text-suave fw-normal">(0 si no aplica)</span></p>
            <div class="row">
                <x-campo class="col-md-4" nombre="precio_auto" etiqueta="🚗 Automóvil" tipo="number" min="0" step="500" :valor="$servicio->precio_auto ?? 0" requerido />
                <x-campo class="col-md-4" nombre="precio_camioneta" etiqueta="🚙 Camioneta" tipo="number" min="0" step="500" :valor="$servicio->precio_camioneta ?? 0" requerido />
                <x-campo class="col-md-4" nombre="precio_moto" etiqueta="🏍️ Moto" tipo="number" min="0" step="500" :valor="$servicio->precio_moto ?? 0" requerido />
            </div>
            <div class="row align-items-end">
                <x-campo class="col-md-6" nombre="duracion_minutos" etiqueta="Duración aproximada (minutos)" tipo="number" min="5" :valor="$servicio->duracion_minutos" requerido />
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input type="hidden" name="activo" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" name="activo" id="activo" value="1" @checked(old('activo', $servicio->activo))>
                        <label class="form-check-label fw-bold" for="activo">Servicio disponible</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('servicios.index') }}" class="btn btn-blanco">Cancelar</a>
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
