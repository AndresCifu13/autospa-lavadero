@extends('layouts.app')
@section('titulo', $producto->exists ? 'Editar producto' : 'Nuevo producto')

@section('contenido')
<x-encabezado :titulo="$producto->exists ? 'Editar producto' : 'Nuevo producto'" icono="bi-box-seam" />

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ $producto->exists ? route('productos.update', $producto) : route('productos.store') }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf
            @if ($producto->exists) @method('PUT') @endif

            <div class="row">
                <x-campo class="col-md-7" nombre="nombre" etiqueta="Nombre" :valor="$producto->nombre" requerido />
                <x-campo class="col-md-5" nombre="categoria" etiqueta="Categoría" :valor="$producto->categoria" list="listaCategorias" placeholder="Limpieza" />
                <datalist id="listaCategorias">
                    @foreach ($categorias as $categoria)<option value="{{ $categoria }}">@endforeach
                </datalist>
            </div>
            <div class="mb-3">
                <label class="form-label" for="descripcion">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>
            <div class="row">
                <x-campo class="col-md-4" nombre="precio" etiqueta="Precio de venta ($)" tipo="number" min="0" step="100" :valor="$producto->precio" requerido />
                <x-campo class="col-6 col-md-4" nombre="stock" etiqueta="Unidades en stock" tipo="number" min="0" :valor="$producto->stock ?? 0" requerido />
                <x-campo class="col-6 col-md-4" nombre="stock_minimo" etiqueta="Avisar cuando queden" tipo="number" min="0" :valor="$producto->stock_minimo" requerido ayuda="Llega un correo al admin." />
            </div>
            <div class="form-check form-switch mb-3">
                <input type="hidden" name="activo" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="activo" id="activo" value="1" @checked(old('activo', $producto->activo))>
                <label class="form-check-label fw-bold" for="activo">Disponible para la venta</label>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('productos.index') }}" class="btn btn-blanco">Cancelar</a>
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
