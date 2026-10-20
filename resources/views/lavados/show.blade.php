@extends('layouts.app')
@section('titulo', 'Vehículo '.$lavado->vehiculo->placa)

@section('contenido')
<x-encabezado titulo="Servicio {{ $lavado->codigo() }}" subtitulo="Registrado {{ $lavado->created_at->translatedFormat('d M Y, h:i a') }} por {{ $lavado->registradoPor->name }}">
    <a href="{{ route('lavados.index') }}" class="btn btn-blanco no-imprimir"><i class="bi bi-arrow-left"></i> Volver</a>
    <button onclick="window.print()" class="btn btn-blanco no-imprimir"><i class="bi bi-printer"></i> Imprimir</button>
    @can('delete', $lavado)
        <x-boton-eliminar :ruta="route('lavados.destroy', $lavado)" texto="Eliminar" class="no-imprimir"
                          mensaje="Se eliminará este registro. Si ya estaba pagado, se descuenta de la tarjeta del cliente." />
    @endcan
</x-encabezado>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="tarjeta tarjeta-cuerpo">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="placa fs-5">{{ $lavado->vehiculo->placa }}</span>
                    <div class="text-suave mt-1"><i class="bi {{ $lavado->vehiculo->tipo->icono() }}"></i> {{ $lavado->vehiculo->tipo->etiqueta() }} {{ $lavado->vehiculo->descripcion() }}</div>
                </div>
                <x-estado :estado="$lavado->estado" />
            </div>

            {{-- Línea de tiempo de estados --}}
            <div class="d-flex gap-1 mb-4">
                @foreach (\App\Enums\EstadoLavado::cases() as $estado)
                    @php($hecho = array_search($estado, \App\Enums\EstadoLavado::cases()) <= array_search($lavado->estado, \App\Enums\EstadoLavado::cases()))
                    <div class="flex-fill text-center">
                        <div class="rounded-pill mb-1 {{ $hecho ? $estado->color() : 'pastel-gris' }}" style="height:8px"></div>
                        <small class="{{ $hecho ? 'fw-bold' : 'text-suave' }}" style="font-size:.72rem">{{ $estado->etiqueta() }}</small>
                    </div>
                @endforeach
            </div>

            <table class="table tabla mb-0">
                <thead><tr><th>Servicio</th><th class="text-end">Precio</th></tr></thead>
                <tbody>
                    @foreach ($lavado->servicios as $servicio)
                        <tr><td>{{ $servicio->nombre }}</td><td class="text-end">{{ dinero($servicio->pivot->precio) }}</td></tr>
                    @endforeach
                    @if ($lavado->descuento > 0)
                        <tr><td class="text-success">Descuento (lavada gratis 🎁)</td><td class="text-end text-success">- {{ dinero($lavado->descuento) }}</td></tr>
                    @endif
                    <tr><td class="fw-black fs-5">Total</td><td class="text-end fw-black fs-5">{{ dinero($lavado->total) }}</td></tr>
                </tbody>
            </table>

            <div class="row small mt-3 g-2">
                <div class="col-6"><span class="text-suave">Lava:</span> <b>{{ $lavado->empleado?->name ?? 'Sin asignar' }}</b></div>
                <div class="col-6"><span class="text-suave">Pago:</span>
                    @if ($lavado->pagado)
                        <b class="text-success">{{ $lavado->es_gratis ? 'Gratis' : $lavado->metodo_pago?->etiqueta() }}</b> · {{ $lavado->pagado_en?->format('h:i a') }}
                    @else
                        <b class="text-danger">Pendiente</b>
                    @endif
                </div>
                @if ($lavado->observaciones)
                    <div class="col-12"><span class="text-suave">Observaciones:</span> {{ $lavado->observaciones }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5 d-flex flex-column gap-3 no-imprimir">
        {{-- Acciones --}}
        <div class="tarjeta tarjeta-cuerpo">
            <h2 class="tarjeta-titulo"><i class="bi bi-lightning-charge"></i> Acciones</h2>

            @if ($lavado->puedeAvanzar())
                @can('avanzar', $lavado)
                    <form method="POST" action="{{ route('lavados.avanzar', $lavado) }}" class="mb-3">
                        @csrf @method('PATCH')
                        <button class="btn btn-verde w-100"><i class="bi bi-arrow-right-circle me-1"></i> Pasar a "{{ $lavado->estado->siguiente()->etiqueta() }}"</button>
                    </form>
                @else
                    <p class="small text-suave">Este vehículo lo tiene asignado otro empleado.</p>
                @endcan
            @endif

            @unless ($lavado->pagado)
                <form method="POST" action="{{ route('lavados.pagar', $lavado) }}">
                    @csrf @method('PATCH')
                    <label class="form-label">Cobrar {{ dinero($lavado->total) }}</label>
                    <div class="row g-2 mb-2">
                        @foreach ($metodos as $metodo)
                            <div class="col-4 opcion-check">
                                <input type="radio" name="metodo_pago" id="m_{{ $metodo->value }}" value="{{ $metodo->value }}" @checked($loop->first)>
                                <label for="m_{{ $metodo->value }}" class="text-center small fw-bold py-2">{{ $metodo->etiqueta() }}</label>
                            </div>
                        @endforeach
                    </div>
                    <button class="btn btn-principal w-100"><i class="bi bi-cash-coin me-1"></i> Marcar como pagado</button>
                </form>
            @else
                <div class="alerta pastel-verde"><i class="bi bi-check-circle-fill"></i> Pagado</div>
            @endunless
        </div>

        {{-- Cliente y fidelización --}}
        <div class="tarjeta tarjeta-cuerpo">
            <h2 class="tarjeta-titulo"><i class="bi bi-person-heart"></i> Cliente</h2>
            <a href="{{ route('clientes.show', $lavado->cliente) }}" class="fw-bold">{{ $lavado->cliente->nombre }}</a>
            <div class="small text-suave mb-3">{{ $lavado->cliente->telefono }} {{ $lavado->cliente->email ? '· '.$lavado->cliente->email : '' }}</div>
            <div class="small fw-bold mb-2">Tarjeta de fidelización ({{ $progreso['acumuladas'] }}/{{ $progreso['meta'] }})</div>
            <x-sellos :progreso="$progreso" />
            @if ($progreso['gratis'] > 0)
                <div class="etiqueta pastel-lila mt-3">🎁 {{ $progreso['gratis'] }} lavada(s) gratis disponible(s)</div>
            @endif
        </div>
    </div>
</div>
@endsection
