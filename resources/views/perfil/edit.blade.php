@extends('layouts.app')
@section('titulo', 'Mi perfil')

@section('contenido')
<x-encabezado titulo="Mi perfil" icono="bi-person-circle" />

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('perfil.update') }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf @method('PUT')
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="avatar avatar-lg">{{ $usuario->iniciales() }}</span>
                <div>
                    <div class="fw-bold">{{ $usuario->name }}</div>
                    <span class="etiqueta pastel-azul">{{ $usuario->rol->etiqueta() }}</span>
                </div>
            </div>

            <x-campo nombre="name" etiqueta="Nombre" :valor="$usuario->name" requerido />
            <div class="row">
                <x-campo class="col-md-6" nombre="email" etiqueta="Correo" tipo="email" :valor="$usuario->email" requerido />
                <x-campo class="col-md-6" nombre="telefono" etiqueta="Teléfono" tipo="tel" :valor="$usuario->telefono" />
            </div>

            <h2 class="h6 fw-bold mt-2">Cambiar contraseña <span class="text-suave fw-normal small">(opcional)</span></h2>
            <x-campo nombre="password_actual" etiqueta="Contraseña actual" tipo="password" autocomplete="current-password" />
            <div class="row">
                <x-campo class="col-md-6" nombre="password" etiqueta="Nueva contraseña" tipo="password" autocomplete="new-password" />
                <x-campo class="col-md-6" nombre="password_confirmation" etiqueta="Repetir nueva contraseña" tipo="password" autocomplete="new-password" />
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
