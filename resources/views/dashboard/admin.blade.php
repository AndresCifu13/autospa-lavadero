@extends('layouts.app')
@section('titulo', 'Inicio')

@section('contenido')
<x-encabezado titulo="Hola, {{ explode(' ', auth()->user()->name)[0] }} 👋" subtitulo="{{ ucfirst(now()->translatedFormat('l d \d\e F')) }} · así va el día">
    <a href="{{ route('reportes.diario') }}" class="btn btn-blanco"><i class="bi bi-bar-chart-line me-1"></i> Reporte del día</a>
    <a href="{{ route('lavados.create') }}" class="btn btn-rosa"><i class="bi bi-plus-lg me-1"></i> Registrar vehículo</a>
</x-encabezado>

{{-- Números del día --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat pastel-azul">
            <i class="bi bi-cash-coin stat-icono"></i>
            <div class="stat-valor">{{ dinero($ingresosHoy) }}</div>
            <div class="stat-texto">Ingresos de hoy</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat pastel-verde">
            <i class="bi bi-car-front stat-icono"></i>
            <div class="stat-valor">{{ $lavadosHoy->count() }}</div>
            <div class="stat-texto">Vehículos hoy · {{ $enProceso }} en cola</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat pastel-amarillo">
            <i class="bi bi-hourglass-split stat-icono"></i>
            <div class="stat-valor">{{ dinero($porCobrar) }}</div>
            <div class="stat-texto">Por cobrar</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat pastel-rosa">
            <i class="bi bi-wallet2 stat-icono"></i>
            <div class="stat-valor">{{ dinero($gastosHoy) }}</div>
            <div class="stat-texto">Gastos de hoy</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Últimos vehículos --}}
    <div class="col-lg-8">
        <div class="tarjeta h-100">
            <div class="tarjeta-cuerpo pb-0 d-flex justify-content-between align-items-center">
                <h2 class="tarjeta-titulo mb-3"><i class="bi bi-list-check"></i> Últimos vehículos</h2>
                <a href="{{ route('lavados.index') }}" class="small fw-bold mb-3">Ver todos</a>
            </div>
            @forelse ($lavadosHoy->take(6) as $lavado)
                <a href="{{ route('lavados.show', $lavado) }}" class="item-lista d-flex align-items-center gap-3 text-decoration-none text-reset">
                    <span class="avatar pastel-azul"><i class="bi {{ $lavado->vehiculo->tipo->icono() }}"></i></span>
                    <div class="flex-grow-1 min-w-0">
                        <span class="placa">{{ $lavado->vehiculo->placa }}</span>
                        <div class="small text-suave text-truncate">{{ $lavado->cliente->nombre }} · {{ $lavado->empleado?->name ?? 'Sin asignar' }}</div>
                    </div>
                    <div class="text-end">
                        <x-estado :estado="$lavado->estado" />
                        <div class="small fw-bold mt-1">{{ dinero($lavado->total) }}
                            @unless ($lavado->pagado)<span class="text-danger" title="Sin pagar">•</span>@endunless
                        </div>
                    </div>
                </a>
            @empty
                <x-vacio icono="bi-car-front" texto="Todavía no han llegado vehículos hoy.">
                    <a href="{{ route('lavados.create') }}" class="btn btn-suave btn-sm">Registrar el primero</a>
                </x-vacio>
            @endforelse
        </div>
    </div>

    <div class="col-lg-4 d-flex flex-column gap-3">
        {{-- Gráfica de la semana --}}
        <div class="tarjeta">
            <div class="tarjeta-cuerpo">
                <h2 class="tarjeta-titulo"><i class="bi bi-graph-up-arrow"></i> Ingresos de la semana</h2>
                @php($maximo = max($semana->max(), 1))
                <div class="grafica">
                    @foreach ($semana as $dia => $valor)
                        <div class="barra-col" title="{{ $dia }}: {{ dinero($valor) }}">
                            <div class="barra" style="height: {{ max(3, round($valor * 100 / $maximo)) }}%"></div>
                            <small>{{ $dia }}</small>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between small mt-3">
                    <span class="text-suave">Lavados hoy: <b class="text-body">{{ dinero($ingresosLavados) }}</b></span>
                    <span class="text-suave">Tienda: <b class="text-body">{{ dinero($ingresosTienda) }}</b></span>
                </div>
            </div>
        </div>

        {{-- Stock bajo --}}
        <div class="tarjeta">
            <div class="tarjeta-cuerpo">
                <h2 class="tarjeta-titulo"><i class="bi bi-box-seam"></i> Productos por acabarse</h2>
                @forelse ($stockBajo as $producto)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="fw-semibold small">{{ $producto->nombre }}</span>
                        <span class="etiqueta pastel-rosa">{{ $producto->stock }} und</span>
                    </div>
                @empty
                    <p class="text-suave small mb-0"><i class="bi bi-check2-circle"></i> Todo el inventario está bien.</p>
                @endforelse
                @if ($stockBajo->isNotEmpty())
                    <a href="{{ route('productos.index', ['stock_bajo' => 1]) }}" class="small fw-bold d-inline-block mt-2">Ver inventario</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
