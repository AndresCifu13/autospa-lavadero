{{-- Barra de abajo, solo en celular (como una app) --}}
<nav class="nav-inferior d-xl-none no-imprimir" aria-label="Menú rápido">
    <a href="{{ route('dashboard') }}" class="{{ activo('dashboard') }}"><i class="bi bi-house-heart"></i>Inicio</a>
    <a href="{{ route('lavados.index') }}" class="{{ activo('lavados.index', 'lavados.show') }}"><i class="bi bi-list-check"></i>Del día</a>
    <a href="{{ route('lavados.create') }}" class="boton-central"><i class="bi bi-plus-lg"></i>Registrar</a>
    <a href="{{ route('ventas.create') }}" class="{{ activo('ventas.*', 'productos.*') }}"><i class="bi bi-shop"></i>Tienda</a>
    <a href="{{ route('clientes.index') }}" class="{{ activo('clientes.*') }}"><i class="bi bi-people"></i>Clientes</a>
</nav>
