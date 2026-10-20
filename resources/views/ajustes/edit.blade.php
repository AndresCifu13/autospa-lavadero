@extends('layouts.app')
@section('titulo', 'Configuración')

@section('contenido')
<x-encabezado titulo="Configuración" icono="bi-sliders" subtitulo="Ajustes generales del lavadero" />

<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="POST" action="{{ route('ajustes.update') }}" class="tarjeta tarjeta-cuerpo" novalidate>
            @csrf @method('PUT')
            <x-campo nombre="nombre_negocio" etiqueta="Nombre del negocio" :valor="$nombreNegocio" requerido />
            <x-campo nombre="lavadas_para_gratis" etiqueta="¿Cada cuántas lavadas pagadas se gana una gratis?" tipo="number" min="1" max="50"
                     :valor="$lavadasParaGratis" requerido ayuda="Ejemplo: 10 = al pagar la lavada número 10, la siguiente es gratis." />
            <x-campo nombre="correo_reportes" etiqueta="Correo donde llegan los reportes" tipo="email" :valor="$correoReportes"
                     ayuda="El reporte diario se envía solo a las 8:00 p.m. (si el programador de tareas está activo)." />
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
