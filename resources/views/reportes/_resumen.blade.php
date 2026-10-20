{{-- Parte común de los reportes diario y mensual --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="stat pastel-azul"><div class="stat-texto">Ingresos lavados</div><div class="stat-valor">{{ dinero($reporte->ingresosLavados) }}</div><div class="small">{{ $reporte->cantidadLavados }} vehículos</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat pastel-lila"><div class="stat-texto">Ingresos tienda</div><div class="stat-valor">{{ dinero($reporte->ingresosTienda) }}</div><div class="small">{{ $reporte->cantidadVentas }} ventas</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat pastel-rosa"><div class="stat-texto">Gastos</div><div class="stat-valor">{{ dinero($reporte->totalGastos) }}</div><div class="small">&nbsp;</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat {{ $reporte->utilidad() >= 0 ? 'pastel-verde' : 'pastel-amarillo' }}"><div class="stat-texto">Utilidad</div><div class="stat-valor">{{ dinero($reporte->utilidad()) }}</div><div class="small">Ingresos − gastos</div></div></div>
</div>

@if ($reporte->lavadosPendientesDePago || $reporte->lavadasGratis)
    <div class="d-flex flex-wrap gap-2 mb-3">
        @if ($reporte->lavadosPendientesDePago)<span class="etiqueta pastel-amarillo"><i class="bi bi-exclamation-circle"></i> {{ $reporte->lavadosPendientesDePago }} lavados sin pagar (no suman en ingresos)</span>@endif
        @if ($reporte->lavadasGratis)<span class="etiqueta pastel-lila">🎁 {{ $reporte->lavadasGratis }} lavadas gratis</span>@endif
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-6">
        <div class="tarjeta tarjeta-cuerpo h-100">
            <h2 class="tarjeta-titulo"><i class="bi bi-stars"></i> Servicios más pedidos</h2>
            @forelse ($reporte->porServicio as $fila)
                <div class="d-flex justify-content-between py-2 border-bottom small"><span>{{ $fila['nombre'] }} <span class="text-suave">× {{ $fila['cantidad'] }}</span></span><b>{{ dinero($fila['total']) }}</b></div>
            @empty
                <p class="text-suave small mb-0">Sin datos.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-6">
        <div class="tarjeta tarjeta-cuerpo h-100">
            <h2 class="tarjeta-titulo"><i class="bi bi-person-badge"></i> Por empleado</h2>
            @forelse ($reporte->porEmpleado as $fila)
                <div class="d-flex justify-content-between py-2 border-bottom small"><span>{{ $fila['nombre'] }} <span class="text-suave">· {{ $fila['cantidad'] }} vehículos</span></span><b>{{ dinero($fila['total']) }}</b></div>
            @empty
                <p class="text-suave small mb-0">Sin datos.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-6">
        <div class="tarjeta tarjeta-cuerpo h-100">
            <h2 class="tarjeta-titulo"><i class="bi bi-credit-card"></i> Por método de pago</h2>
            @forelse ($reporte->porMetodoPago as $metodo => $valor)
                <div class="d-flex justify-content-between py-2 border-bottom small"><span>{{ $metodo }}</span><b>{{ dinero($valor) }}</b></div>
            @empty
                <p class="text-suave small mb-0">Sin datos.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-6">
        <div class="tarjeta tarjeta-cuerpo h-100">
            <h2 class="tarjeta-titulo"><i class="bi bi-wallet2"></i> Gastos por categoría</h2>
            @forelse ($reporte->gastosPorCategoria as $categoria => $valor)
                <div class="d-flex justify-content-between py-2 border-bottom small"><span>{{ $categoria }}</span><b>{{ dinero($valor) }}</b></div>
            @empty
                <p class="text-suave small mb-0">Sin gastos.</p>
            @endforelse
        </div>
    </div>
</div>
