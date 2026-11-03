{{-- Cambiar entre reporte diario, semanal y mensual --}}
<div class="dropdown mb-3 no-imprimir">
    @php($vistas = [
        'reportes.diario' => ['Ver diario', 'bi-calendar-day'],
        'reportes.semanal' => ['Ver semanal', 'bi-calendar-week'],
        'reportes.mensual' => ['Ver mensual', 'bi-calendar3'],
    ])
    @php($actual = $vistas[request()->route()->getName()] ?? $vistas['reportes.diario'])
    <button class="btn btn-blanco dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi {{ $actual[1] }} me-1"></i> {{ $actual[0] }}
    </button>
    <ul class="dropdown-menu">
        @foreach ($vistas as $ruta => [$texto, $icono])
            <li><a class="dropdown-item {{ activo($ruta) }}" href="{{ route($ruta) }}"><i class="bi {{ $icono }} me-2"></i>{{ $texto }}</a></li>
        @endforeach
    </ul>
</div>
