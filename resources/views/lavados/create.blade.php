@extends('layouts.app')
@section('titulo', 'Registrar vehículo')

@section('contenido')
<x-encabezado titulo="Registrar vehículo" icono="bi-plus-circle" subtitulo="Escribe la placa: si ya vino antes, los datos se llenan solos." />

<form method="POST" action="{{ route('lavados.store') }}" id="formLavado" novalidate>
    @csrf
    <input type="hidden" name="cliente_id" id="cliente_id" value="{{ old('cliente_id') }}">

    <div class="row g-3">
        <div class="col-lg-8 d-flex flex-column gap-3">

            {{-- 1. Vehículo --}}
            <div class="tarjeta tarjeta-cuerpo">
                <h2 class="tarjeta-titulo"><span class="etiqueta pastel-azul">1</span> Vehículo</h2>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                        <input type="text" id="placa" name="placa" maxlength="10" required autofocus autocomplete="off"
                               value="{{ old('placa', $placa) }}" placeholder="ABC123"
                               @class(['form-control placa-input', 'is-invalid' => $errors->has('placa')])>
                        @error('placa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="avisoPlaca" class="form-text"></div>
                    </div>
                    <div class="col-md-7">
                        <span class="form-label d-block">Tipo <span class="text-danger">*</span></span>
                        <div class="row g-2">
                            @foreach ($tipos as $tipo)
                                <div class="col-4 opcion-check">
                                    <input type="radio" name="tipo" id="tipo_{{ $tipo->value }}" value="{{ $tipo->value }}" @checked(old('tipo', 'auto') === $tipo->value)>
                                    <label for="tipo_{{ $tipo->value }}" class="text-center">
                                        <i class="bi {{ $tipo->icono() }} fs-3 d-block"></i>
                                        <span class="small fw-bold">{{ $tipo->etiqueta() }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-4"><x-campo nombre="marca" etiqueta="Marca" placeholder="Mazda" class="mb-0" /></div>
                    <div class="col-4"><x-campo nombre="modelo" etiqueta="Modelo" placeholder="2020" class="mb-0" /></div>
                    <div class="col-4"><x-campo nombre="color" etiqueta="Color" placeholder="Rojo" class="mb-0" /></div>
                </div>
            </div>

            {{-- 2. Cliente --}}
            <div class="tarjeta tarjeta-cuerpo">
                <h2 class="tarjeta-titulo"><span class="etiqueta pastel-azul">2</span> Cliente</h2>

                <div id="clienteExistente" class="d-none">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 pastel-verde">
                        <i class="bi bi-person-check fs-3"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold" id="clienteNombre"></div>
                            <div class="small" id="clienteDatos"></div>
                        </div>
                    </div>
                    <div class="mt-3" id="cajaFidelizacion"></div>
                </div>

                <div id="clienteNuevo" class="row g-3">
                    <div class="col-md-6"><x-campo nombre="cliente_nombre" etiqueta="Nombre" class="mb-0" placeholder="Nombre completo" /></div>
                    <div class="col-md-6"><x-campo nombre="cliente_telefono" etiqueta="Teléfono" tipo="tel" class="mb-0" placeholder="300 000 0000" /></div>
                    <div class="col-md-6"><x-campo nombre="cliente_documento" etiqueta="Documento" class="mb-0" /></div>
                    <div class="col-md-6"><x-campo nombre="cliente_email" etiqueta="Correo (para avisarle)" tipo="email" class="mb-0" ayuda="Le llega un correo cuando su vehículo esté listo." /></div>
                </div>
            </div>

            {{-- 3. Servicios --}}
            <div class="tarjeta tarjeta-cuerpo">
                <h2 class="tarjeta-titulo"><span class="etiqueta pastel-azul">3</span> Servicios</h2>
                @error('servicios')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <div class="row g-2">
                    @foreach ($servicios as $servicio)
                        <div class="col-sm-6 opcion-check">
                            <input type="checkbox" name="servicios[]" id="servicio_{{ $servicio->id }}" value="{{ $servicio->id }}"
                                   data-auto="{{ $servicio->precio_auto }}" data-camioneta="{{ $servicio->precio_camioneta }}" data-moto="{{ $servicio->precio_moto }}"
                                   @checked(in_array($servicio->id, old('servicios', [])))>
                            <label for="servicio_{{ $servicio->id }}">
                                <div class="d-flex justify-content-between gap-2">
                                    <span class="fw-bold">{{ $servicio->nombre }}</span>
                                    <span class="fw-black precio-servicio text-nowrap"></span>
                                </div>
                                <div class="small text-suave">{{ $servicio->descripcion }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Resumen --}}
        <div class="col-lg-4">
            <div class="tarjeta tarjeta-cuerpo sticky-lg-top" style="top:90px">
                <h2 class="tarjeta-titulo"><i class="bi bi-receipt"></i> Resumen</h2>

                <x-seleccion nombre="empleado_id" etiqueta="¿Quién lo lava?" :opciones="$empleados->pluck('name', 'id')" vacio="Sin asignar todavía" />
                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="2" class="form-control" placeholder="Rayón en la puerta, objetos de valor...">{{ old('observaciones') }}</textarea>
                </div>

                <div class="form-check form-switch mb-3 d-none p-3 rounded-4 pastel-lila" id="cajaGratis">
                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" name="usar_gratis" id="usar_gratis" value="1" @checked(old('usar_gratis'))>
                    <label class="form-check-label fw-bold" for="usar_gratis">🎁 Usar lavada gratis</label>
                </div>

                <div class="ticket pt-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Total</span>
                    <span class="fs-3 fw-black" id="total">$ 0</span>
                </div>
                <p class="small text-suave mb-3">El precio se calcula según el tipo de vehículo.</p>

                <button type="submit" class="btn btn-rosa w-100 py-2"><i class="bi bi-check2-circle me-1"></i> Registrar ingreso</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
/*
 * Lógica del formulario:
 *  - Calcula el total según el tipo de vehículo (HU-08). El servidor lo vuelve a calcular al guardar.
 *  - Busca la placa para llenar los datos del cliente si ya existe.
 */
(() => {
    const form = document.getElementById('formLavado');
    const placa = document.getElementById('placa');
    const servicios = form.querySelectorAll('input[name="servicios[]"]');
    const usarGratis = document.getElementById('usar_gratis');

    const tipoActual = () => form.querySelector('input[name=tipo]:checked')?.value ?? 'auto';

    function recalcular() {
        const tipo = tipoActual();
        let total = 0;
        servicios.forEach(caja => {
            const precio = parseFloat(caja.dataset[tipo]) || 0;
            const etiquetaPrecio = caja.nextElementSibling.querySelector('.precio-servicio');
            etiquetaPrecio.textContent = precio > 0 ? dinero(precio) : 'No aplica';
            caja.disabled = precio <= 0;          // ej: tapicería no aplica para motos
            if (caja.disabled) caja.checked = false;
            if (caja.checked) total += precio;
        });
        document.getElementById('total').innerHTML = usarGratis.checked
            ? '<s class="text-suave fs-6">' + dinero(total) + '</s> $ 0'
            : dinero(total);
    }

    let temporizador;
    function buscarPlaca() {
        clearTimeout(temporizador);
        const valor = placa.value.replace(/[^a-z0-9]/gi, '').toUpperCase();
        if (valor.length < 5) return mostrarCliente(null);

        temporizador = setTimeout(async () => {
            try {
                const respuesta = await fetch(`{{ route('lavados.buscar-placa') }}?placa=${valor}`, { headers: { 'Accept': 'application/json' } });
                const datos = await respuesta.json();
                mostrarCliente(datos.encontrado ? datos : null);
            } catch (e) {
                document.getElementById('avisoPlaca').textContent = 'No se pudo buscar la placa. Puedes continuar igual.';
            }
        }, 350);
    }

    function mostrarCliente(datos) {
        const aviso = document.getElementById('avisoPlaca');
        document.getElementById('clienteExistente').classList.toggle('d-none', !datos);
        document.getElementById('clienteNuevo').classList.toggle('d-none', !!datos);
        document.getElementById('cajaGratis').classList.toggle('d-none', !(datos && datos.fidelizacion.gratis > 0));
        document.getElementById('cliente_id').value = datos ? datos.cliente.id : '';

        if (!datos) {
            aviso.innerHTML = placa.value.length >= 5 ? '<span class="text-primary"><i class="bi bi-stars"></i> Placa nueva: llena los datos del cliente.</span>' : '';
            usarGratis.checked = false;
            return recalcular();
        }

        aviso.innerHTML = `<span class="text-success"><i class="bi bi-check-circle"></i> Ya nos visitó ${datos.visitas} ${datos.visitas === 1 ? 'vez' : 'veces'}.</span>`;
        document.getElementById('clienteNombre').textContent = datos.cliente.nombre;
        document.getElementById('clienteDatos').textContent = [datos.cliente.telefono, datos.cliente.email].filter(Boolean).join(' · ') || 'Sin datos de contacto';

        const f = datos.fidelizacion;
        document.getElementById('cajaFidelizacion').innerHTML =
            `<div class="small fw-bold mb-1">Tarjeta de fidelización: ${f.acumuladas}/${f.meta}</div>
             <div class="progress" style="height:10px;border-radius:99px"><div class="progress-bar" style="width:${f.porcentaje}%;background:var(--menta-fuerte)"></div></div>
             <div class="small text-suave mt-1">${f.gratis > 0 ? '🎁 Tiene ' + f.gratis + ' lavada(s) gratis disponible(s)' : 'Le faltan ' + f.faltan + ' para la gratis'}</div>`;

        // Seleccionamos su tipo de vehículo y llenamos los campos vacíos
        const radio = form.querySelector(`input[name=tipo][value="${datos.vehiculo.tipo}"]`);
        if (radio) radio.checked = true;
        ['marca', 'modelo', 'color'].forEach(c => { if (!form[c].value) form[c].value = datos.vehiculo[c] ?? ''; });
        recalcular();
    }

    form.querySelectorAll('input[name=tipo]').forEach(r => r.addEventListener('change', recalcular));
    servicios.forEach(c => c.addEventListener('change', recalcular));
    usarGratis.addEventListener('change', recalcular);
    placa.addEventListener('input', buscarPlaca);

    recalcular();
    if (placa.value) buscarPlaca();
})();
</script>
@endpush
