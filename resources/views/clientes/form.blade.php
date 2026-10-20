@extends('layouts.app')
@section('titulo', $cliente->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('contenido')
<x-encabezado :titulo="$cliente->exists ? 'Editar cliente' : 'Nuevo cliente'" icono="bi-person" />

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ $cliente->exists ? route('clientes.update', $cliente) : route('clientes.store') }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf
            @if ($cliente->exists) @method('PUT') @endif

            <x-campo nombre="nombre" etiqueta="Nombre completo" :valor="$cliente->nombre" requerido />
            <div class="row">
                <x-campo class="col-md-6" nombre="documento" etiqueta="Documento" :valor="$cliente->documento" />
                <x-campo class="col-md-6" nombre="telefono" etiqueta="Teléfono" tipo="tel" :valor="$cliente->telefono" />
            </div>
            <x-campo nombre="email" etiqueta="Correo" tipo="email" :valor="$cliente->email" ayuda="Para avisarle cuando su vehículo esté listo y cuando gane una lavada gratis." />

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ url()->previous() }}" class="btn btn-blanco">Cancelar</a>
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
