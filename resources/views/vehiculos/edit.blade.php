@extends('layouts.app')
@section('titulo', 'Editar vehículo')

@section('contenido')
<x-encabezado titulo="Editar vehículo" icono="bi-car-front" subtitulo="Dueño: {{ $vehiculo->cliente->nombre }}" />

<div class="row justify-content-center">
    <div class="col-lg-6">
        <form method="POST" action="{{ route('vehiculos.update', $vehiculo) }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf @method('PUT')
            <input type="hidden" name="cliente_id" value="{{ $vehiculo->cliente_id }}">
            <div class="row">
                <x-campo class="col-6" nombre="placa" etiqueta="Placa" :valor="$vehiculo->placa" requerido />
                <x-seleccion class="col-6" nombre="tipo" etiqueta="Tipo" :opciones="$tipos" :valor="$vehiculo->tipo->value" />
                <x-campo class="col-4" nombre="marca" etiqueta="Marca" :valor="$vehiculo->marca" />
                <x-campo class="col-4" nombre="modelo" etiqueta="Modelo" :valor="$vehiculo->modelo" />
                <x-campo class="col-4" nombre="color" etiqueta="Color" :valor="$vehiculo->color" />
            </div>
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('clientes.show', $vehiculo->cliente_id) }}" class="btn btn-blanco">Cancelar</a>
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
