{{-- Menú principal. Lo que se muestra depende del rol del usuario. --}}
@php($usuario = auth()->user())
<nav class="navbar navbar-expand-xl navbar-autospa sticky-top no-imprimir">
    <div class="container-xl">
        <a href="{{ route('dashboard') }}" class="navbar-brand">@include('partials.marca')</a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mt-3 mt-xl-0">
                <li class="nav-item">
                    <a class="nav-link {{ activo('dashboard') }}" href="{{ route('dashboard') }}"><i class="bi bi-house-heart me-1"></i> Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ activo('lavados.*') }}" href="{{ route('lavados.index') }}"><i class="bi bi-car-front me-1"></i> Del día</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ activo('clientes.*', 'vehiculos.*') }}" href="{{ route('clientes.index') }}"><i class="bi bi-people me-1"></i> Clientes</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ activo('ventas.*', 'productos.*') }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shop me-1"></i> Tienda
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item {{ activo('ventas.create') }}" href="{{ route('ventas.create') }}"><i class="bi bi-cart-plus me-2"></i>Nueva venta</a></li>
                        <li><a class="dropdown-item {{ activo('ventas.index', 'ventas.show') }}" href="{{ route('ventas.index') }}"><i class="bi bi-receipt me-2"></i>Ventas del día</a></li>
                        <li><a class="dropdown-item {{ activo('productos.*') }}" href="{{ route('productos.index') }}"><i class="bi bi-box-seam me-2"></i>Productos</a></li>
                    </ul>
                </li>

                @if ($usuario->esAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ activo('trabajadores.*', 'servicios.*', 'gastos.*', 'ajustes.*') }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear me-1"></i> Administración
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ activo('trabajadores.*') }}" href="{{ route('trabajadores.index') }}"><i class="bi bi-person-badge me-2"></i>Trabajadores</a></li>
                            <li><a class="dropdown-item {{ activo('servicios.*') }}" href="{{ route('servicios.index') }}"><i class="bi bi-stars me-2"></i>Servicios y precios</a></li>
                            <li><a class="dropdown-item {{ activo('gastos.*') }}" href="{{ route('gastos.index') }}"><i class="bi bi-wallet2 me-2"></i>Gastos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item {{ activo('ajustes.*') }}" href="{{ route('ajustes.edit') }}"><i class="bi bi-sliders me-2"></i>Configuración</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ activo('reportes.*') }}" href="{{ route('reportes.diario') }}"><i class="bi bi-bar-chart-line me-1"></i> Reportes</a>
                    </li>
                @endif
            </ul>

            <div class="d-flex align-items-center gap-2 mt-3 mt-xl-0 pb-3 pb-xl-0">
                <a href="{{ route('lavados.create') }}" class="btn btn-rosa btn-sm d-none d-xxl-inline-flex align-items-center gap-1">
                    <i class="bi bi-plus-lg"></i> Registrar vehículo
                </a>
                <div class="dropdown">
                    <button class="btn p-0 border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar">{{ $usuario->iniciales() }}</span>
                        <span class="text-start d-xl-none d-xxl-inline">
                            <span class="d-block fw-bold small lh-1">{{ $usuario->name }}</span>
                            <span class="d-block text-suave" style="font-size:.72rem">{{ $usuario->rol->etiqueta() }}</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('perfil.edit') }}"><i class="bi bi-person-circle me-2"></i>Mi perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
