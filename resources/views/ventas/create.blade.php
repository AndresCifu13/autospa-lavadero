@extends('layouts.app')
@section('titulo', 'Nueva venta')

@section('contenido')
<x-encabezado titulo="Nueva venta" icono="bi-cart-plus" subtitulo="Toca un producto para agregarlo" />

<form method="POST" action="{{ route('ventas.store') }}" id="formVenta" novalidate>
    @csrf
    <div class="row g-3">
        {{-- Catálogo --}}
        <div class="col-lg-7">
            <input type="search" id="filtro" class="form-control mb-3" placeholder="Buscar producto...">
            <div class="row g-2" id="catalogo">
                @forelse ($productos as $producto)
                    <div class="col-6 col-md-4 producto-item" data-nombre="{{ mb_strtolower($producto->nombre.' '.$producto->categoria) }}">
                        <button type="button" class="tarjeta tarjeta-cuerpo w-100 h-100 text-start border-0 btn-agregar"
                                data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}" data-precio="{{ $producto->precio }}" data-stock="{{ $producto->stock }}">
                            <span class="etiqueta pastel-lila mb-2">{{ $producto->categoria ?? 'General' }}</span>
                            <div class="fw-bold small">{{ $producto->nombre }}</div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-black">{{ dinero($producto->precio) }}</span>
                                <span class="small text-suave">{{ $producto->stock }} und</span>
                            </div>
                        </button>
                    </div>
                @empty
                    <div class="col-12"><div class="tarjeta"><x-vacio icono="bi-box-seam" texto="No hay productos con stock." /></div></div>
                @endforelse
            </div>
        </div>

        {{-- Carrito --}}
        <div class="col-lg-5">
            <div class="tarjeta tarjeta-cuerpo sticky-lg-top" style="top:90px">
                <h2 class="tarjeta-titulo"><i class="bi bi-cart3"></i> Carrito</h2>
                <div id="carrito"></div>
                <p id="carritoVacio" class="text-suave small">Todavía no has agregado productos.</p>

                <div class="mb-3 mt-3">
                    <label class="form-label" for="cliente_id">Cliente (opcional)</label>
                    <select name="cliente_id" id="cliente_id" class="form-select">
                        <option value="">Cliente de mostrador</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>{{ $cliente->nombre }} {{ $cliente->documento ? '('.$cliente->documento.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <x-seleccion nombre="metodo_pago" etiqueta="Método de pago" :opciones="$metodos" valor="efectivo" />

                <div class="ticket pt-3 d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold">Total</span>
                    <span class="fs-3 fw-black" id="totalVenta">$ 0</span>
                </div>
                <button type="submit" class="btn btn-rosa w-100 py-2" id="btnVender" disabled><i class="bi bi-bag-check me-1"></i> Registrar venta</button>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
/* Carrito de compras sencillo. El servidor vuelve a validar precios y stock. */
(() => {
    const carrito = {}; // { id: {nombre, precio, stock, cantidad} }
    const contenedor = document.getElementById('carrito');

    document.querySelectorAll('.btn-agregar').forEach(boton => {
        boton.addEventListener('click', () => {
            const { id, nombre, precio, stock } = boton.dataset;
            const item = carrito[id] ??= { nombre, precio: +precio, stock: +stock, cantidad: 0 };
            if (item.cantidad < item.stock) item.cantidad++;
            dibujar();
        });
    });

    function cambiar(id, delta) {
        const item = carrito[id];
        item.cantidad = Math.min(item.stock, item.cantidad + delta);
        if (item.cantidad <= 0) delete carrito[id];
        dibujar();
    }

    function dibujar() {
        const ids = Object.keys(carrito);
        let total = 0;
        contenedor.innerHTML = ids.map((id, i) => {
            const item = carrito[id];
            total += item.precio * item.cantidad;
            return `
              <div class="d-flex align-items-center gap-2 py-2 border-bottom">
                <input type="hidden" name="items[${i}][producto_id]" value="${id}">
                <input type="hidden" name="items[${i}][cantidad]" value="${item.cantidad}">
                <div class="flex-grow-1 small"><div class="fw-bold">${item.nombre}</div>${dinero(item.precio)} c/u</div>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-blanco" data-cambiar="${id}" data-delta="-1">−</button>
                  <span class="btn btn-blanco disabled fw-bold">${item.cantidad}</span>
                  <button type="button" class="btn btn-blanco" data-cambiar="${id}" data-delta="1" ${item.cantidad >= item.stock ? 'disabled' : ''}>+</button>
                </div>
                <div class="fw-bold small text-end" style="min-width:72px">${dinero(item.precio * item.cantidad)}</div>
              </div>`;
        }).join('');
        contenedor.querySelectorAll('[data-cambiar]').forEach(b => b.addEventListener('click', () => cambiar(b.dataset.cambiar, +b.dataset.delta)));
        document.getElementById('totalVenta').textContent = dinero(total);
        document.getElementById('carritoVacio').classList.toggle('d-none', ids.length > 0);
        document.getElementById('btnVender').disabled = ids.length === 0;
    }

    document.getElementById('filtro').addEventListener('input', e => {
        const texto = e.target.value.toLowerCase();
        document.querySelectorAll('.producto-item').forEach(p => p.classList.toggle('d-none', !p.dataset.nombre.includes(texto)));
    });
})();
</script>
@endpush
