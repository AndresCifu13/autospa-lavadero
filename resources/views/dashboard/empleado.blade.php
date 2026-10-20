@extends('layouts.app')
@section('titulo', 'Inicio')

@section('contenido')
<x-encabezado titulo="Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋" subtitulo="Estos son tus vehículos de hoy">
    <a href="{{ route('lavados.create') }}" class="btn btn-rosa d-none d-lg-inline-block"><i class="bi bi-plus-lg me-1"></i> Registrar vehículo</a>
</x-encabezado>

<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="stat pastel-azul text-center">
            <div class="stat-valor">{{ $misLavados->count() }}</div>
            <div class="stat-texto">Asignados</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat pastel-amarillo text-center">
            <div class="stat-valor">{{ $sinAsignar->count() }}</div>
            <div class="stat-texto">Sin asignar</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat pastel-verde text-center">
            <div class="stat-valor">{{ $terminadosHoy }}</div>
            <div class="stat-texto">Terminados</div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="tarjeta">
            <div class="tarjeta-cuerpo pb-2"><h2 class="tarjeta-titulo mb-0"><i class="bi bi-person-check"></i> Mis vehículos</h2></div>
            @forelse ($misLavados as $lavado)
                <div class="item-lista">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <span class="placa">{{ $lavado->vehiculo->placa }}</span>
                            <span class="small text-suave ms-1">{{ $lavado->vehiculo->tipo->etiqueta() }}</span>
                            <div class="small mt-1">{{ $lavado->servicios->pluck('nombre')->implode(', ') }}</div>
                        </div>
                        <x-estado :estado="$lavado->estado" />
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        @if ($lavado->puedeAvanzar())
                            <form method="POST" action="{{ route('lavados.avanzar', $lavado) }}">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-verde"><i class="bi bi-arrow-right-circle me-1"></i> Pasar a "{{ $lavado->estado->siguiente()->etiqueta() }}"</button>
                            </form>
                        @endif
                        <a href="{{ route('lavados.show', $lavado) }}" class="btn btn-sm btn-blanco">Ver</a>
                    </div>
                </div>
            @empty
                <x-vacio icono="bi-emoji-smile" texto="No tienes vehículos pendientes. ¡Buen trabajo!" />
            @endforelse
        </div>
    </div>

    <div class="col-lg-5">
        <div class="tarjeta">
            <div class="tarjeta-cuerpo pb-2"><h2 class="tarjeta-titulo mb-0"><i class="bi bi-hourglass"></i> En espera (sin asignar)</h2></div>
            @forelse ($sinAsignar as $lavado)
                <a href="{{ route('lavados.show', $lavado) }}" class="item-lista d-flex justify-content-between align-items-center text-decoration-none text-reset">
                    <div>
                        <span class="placa">{{ $lavado->vehiculo->placa }}</span>
                        <div class="small text-suave mt-1">Llegó {{ $lavado->created_at->diffForHumans() }}</div>
                    </div>
                    <i class="bi bi-chevron-right text-suave"></i>
                </a>
            @empty
                <x-vacio icono="bi-cup-hot" texto="No hay vehículos esperando." />
            @endforelse
        </div>
    </div>
</div>
@endsection
