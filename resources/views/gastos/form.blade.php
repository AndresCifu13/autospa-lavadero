@extends('layouts.app')
@section('titulo', $gasto->exists ? 'Editar gasto' : 'Registrar gasto')

@section('contenido')
<x-encabezado :titulo="$gasto->exists ? 'Editar gasto' : 'Registrar gasto'" icono="bi-wallet2" />

<div class="row justify-content-center">
    <div class="col-lg-6">
        <form method="POST" action="{{ $gasto->exists ? route('gastos.update', $gasto) : route('gastos.store') }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf
            @if ($gasto->exists) @method('PUT') @endif

            <x-campo nombre="descripcion" etiqueta="¿En qué se gastó?" :valor="$gasto->descripcion" placeholder="Compra de shampoo" requerido />
            <x-seleccion nombre="categoria" etiqueta="Categoría" :opciones="$categorias" :valor="$gasto->categoria?->value" vacio="Elige una categoría" requerido />
            <div class="row">
                <x-campo class="col-6" nombre="monto" etiqueta="Valor ($)" tipo="number" min="1" step="100" :valor="$gasto->monto" requerido />
                <x-campo class="col-6" nombre="fecha" etiqueta="Fecha" tipo="date" :valor="$gasto->fecha?->toDateString()" max="{{ today()->toDateString() }}" requerido />
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('gastos.index') }}" class="btn btn-blanco">Cancelar</a>
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
