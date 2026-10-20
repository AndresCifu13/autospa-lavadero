@extends('layouts.app')
@section('titulo', 'Productos')

@section('contenido')
<x-encabezado titulo="Productos de la tienda" icono="bi-box-seam" subtitulo="Inventario disponible">
    <a href="{{ route('ventas.create') }}" class="btn btn-rosa"><i class="bi bi-cart-plus me-1"></i> Nueva venta</a>
    @if (auth()->user()->esAdmin())
        <a href="{{ route('productos.create') }}" class="btn btn-principal"><i class="bi bi-plus-lg me-1"></i> Nuevo producto</a>
    @endif
</x-encabezado>

<form class="tarjeta tarjeta-cuerpo mb-3 row g-2 align-items-center mx-0" method="GET">
    <div class="col-md-7"><input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar producto o categoría"></div>
    <div class="col-7 col-md-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="stock_bajo" id="stock_bajo" value="1" @checked(request('stock_bajo'))>
            <label class="form-check-label" for="stock_bajo">Solo stock bajo</label>
        </div>
    </div>
    <div class="col-5 col-md-2"><button class="btn btn-suave w-100"><i class="bi bi-search"></i></button></div>
</form>

<div class="row g-3">
    @forelse ($productos as $producto)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div @class(['tarjeta tarjeta-cuerpo h-100 d-flex flex-column', 'opacity-50' => ! $producto->activo])>
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="etiqueta pastel-lila">{{ $producto->categoria ?? 'General' }}</span>
                    <span class="etiqueta {{ $producto->stockBajo() ? 'pastel-rosa' : 'pastel-verde' }}">{{ $producto->stock }} und</span>
                </div>
                <div class="fw-bold">{{ $producto->nombre }}</div>
                <div class="small text-suave flex-grow-1">{{ $producto->descripcion }}</div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="fs-5 fw-black">{{ dinero($producto->precio) }}</span>
                    @if (auth()->user()->esAdmin())
                        <span>
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-suave"><i class="bi bi-pencil"></i></a>
                            <x-boton-eliminar :ruta="route('productos.destroy', $producto)" mensaje="Se quitará {{ $producto->nombre }} de la tienda." />
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="tarjeta"><x-vacio icono="bi-box-seam" texto="No hay productos." /></div></div>
    @endforelse
</div>
<div class="mt-3">{{ $productos->links() }}</div>
@endsection
