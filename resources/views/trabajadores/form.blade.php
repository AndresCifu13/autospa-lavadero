@extends('layouts.app')
@section('titulo', $trabajador->exists ? 'Editar trabajador' : 'Nuevo trabajador')

@section('contenido')
<x-encabezado :titulo="$trabajador->exists ? 'Editar trabajador' : 'Agregar trabajador'" icono="bi-person-badge" />

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ $trabajador->exists ? route('trabajadores.update', $trabajador) : route('trabajadores.store') }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf
            @if ($trabajador->exists) @method('PUT') @endif

            <x-campo nombre="name" etiqueta="Nombre completo" :valor="$trabajador->name" requerido />
            <div class="row">
                <x-campo class="col-md-6" nombre="documento" etiqueta="Documento" :valor="$trabajador->documento" />
                <x-campo class="col-md-6" nombre="telefono" etiqueta="Teléfono" tipo="tel" :valor="$trabajador->telefono" />
            </div>
            <x-campo nombre="email" etiqueta="Correo (con este inicia sesión)" tipo="email" :valor="$trabajador->email" requerido />
            <div class="row">
                <x-seleccion class="col-md-6" nombre="rol" etiqueta="Rol" :opciones="$roles" :valor="$trabajador->rol?->value" requerido />
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="hidden" name="activo" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" name="activo" id="activo" value="1" @checked(old('activo', $trabajador->activo))>
                        <label class="form-check-label fw-bold" for="activo">Puede entrar al sistema</label>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-4 pastel-amarillo small mb-3">
                <i class="bi bi-key me-1"></i>
                @if ($trabajador->exists)
                    Deja la contraseña vacía si no la quieres cambiar.
                @else
                    Si dejas la contraseña vacía, el sistema crea una y se la envía por correo.
                @endif
            </div>
            <div class="row">
                <x-campo class="col-md-6" nombre="password" etiqueta="Contraseña" tipo="password" autocomplete="new-password" />
                <x-campo class="col-md-6" nombre="password_confirmation" etiqueta="Repetir contraseña" tipo="password" autocomplete="new-password" />
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('trabajadores.index') }}" class="btn btn-blanco">Cancelar</a>
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
