@extends('layouts.app')
@section('titulo', 'Trabajadores')

@section('contenido')
<x-encabezado titulo="Trabajadores" icono="bi-person-badge" subtitulo="Gestión del personal del lavadero">
    <a href="{{ route('trabajadores.create') }}" class="btn btn-principal"><i class="bi bi-person-plus me-1"></i> Agregar trabajador</a>
</x-encabezado>

<form class="tarjeta tarjeta-cuerpo mb-3 row g-2 mx-0" method="GET">
    <div class="col-md-7"><input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Nombre, correo o documento"></div>
    <div class="col-8 col-md-3">
        <select name="rol" class="form-select">
            <option value="">Todos los roles</option>
            @foreach ($roles as $valor => $texto)<option value="{{ $valor }}" @selected(request('rol') === $valor)>{{ $texto }}</option>@endforeach
        </select>
    </div>
    <div class="col-4 col-md-2"><button class="btn btn-suave w-100"><i class="bi bi-search"></i></button></div>
</form>

<div class="row g-3">
    @forelse ($trabajadores as $trabajador)
        <div class="col-md-6 col-xl-4">
            <div class="tarjeta tarjeta-cuerpo h-100 d-flex flex-column">
                <div class="d-flex gap-3 align-items-center mb-3">
                    <span class="avatar avatar-lg {{ $trabajador->esAdmin() ? 'pastel-rosa' : 'pastel-azul' }}">{{ $trabajador->iniciales() }}</span>
                    <div class="min-w-0">
                        <div class="fw-bold text-truncate">{{ $trabajador->name }}</div>
                        <span class="etiqueta {{ $trabajador->esAdmin() ? 'pastel-rosa' : 'pastel-azul' }}">{{ $trabajador->rol->etiqueta() }}</span>
                        @unless ($trabajador->activo)<span class="etiqueta pastel-gris">Inactivo</span>@endunless
                    </div>
                </div>
                <div class="small text-suave flex-grow-1">
                    <div><i class="bi bi-envelope me-1"></i> {{ $trabajador->email }}</div>
                    <div><i class="bi bi-telephone me-1"></i> {{ $trabajador->telefono ?? '—' }}</div>
                    <div><i class="bi bi-card-text me-1"></i> {{ $trabajador->documento ?? '—' }}</div>
                    <div class="mt-2"><i class="bi bi-car-front me-1"></i> {{ $trabajador->lavados_hoy }} vehículos hoy</div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('trabajadores.edit', $trabajador) }}" class="btn btn-sm btn-suave flex-grow-1"><i class="bi bi-pencil me-1"></i> Editar</a>
                    @unless ($trabajador->is(auth()->user()))
                        <x-boton-eliminar :ruta="route('trabajadores.destroy', $trabajador)" mensaje="Se eliminará a {{ $trabajador->name }}. Sus registros anteriores se conservan." />
                    @endunless
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="tarjeta"><x-vacio icono="bi-person-badge" texto="No se encontraron trabajadores." /></div></div>
    @endforelse
</div>
<div class="mt-3">{{ $trabajadores->links() }}</div>
@endsection
