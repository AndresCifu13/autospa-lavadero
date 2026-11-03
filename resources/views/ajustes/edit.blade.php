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

            <hr class="my-4">
            <h2 class="tarjeta-titulo"><i class="bi bi-envelope"></i> Correos</h2>

            <x-campo nombre="correo_reportes" etiqueta="Correos donde llegan los reportes" :valor="$correoReportes"
                     placeholder="dueno@gmail.com, contador@hotmail.com" autocomplete="off"
                     ayuda="Puedes poner varios correos de cualquier proveedor, separados por coma. Todos los días a las 8 p.m. llega el reporte diario; los domingos también el semanal y el último día del mes el mensual." />

            <x-campo nombre="correos_avisos" etiqueta="Correos que reciben avisos cuando se registra algo" :valor="$correosAvisos"
                     placeholder="dueno@gmail.com, administrador@outlook.com" autocomplete="off"
                     ayuda="Separados por coma. Déjalo vacío si no quieres avisos." />

            <div class="mb-3">
                <label class="form-label">Avisar cuando se registre:</label>
                <div class="row g-2">
                    @foreach (\App\Models\Ajuste::TIPOS_AVISO as $clave => $texto)
                        <div class="col-6 col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="avisos_tipos[]" value="{{ $clave }}" id="aviso_{{ $clave }}"
                                       @checked(in_array($clave, old('avisos_tipos', $avisosActivos)))>
                                <label class="form-check-label" for="aviso_{{ $clave }}">{{ $texto }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('avisos_tipos.*')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-principal"><i class="bi bi-check2 me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
