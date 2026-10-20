{{-- Mensajes que mandan los controladores con ->with('exito') o ->with('error') --}}
@if (session('exito'))
    <div class="alerta pastel-verde alert alert-dismissible fade show alerta-autocerrar mb-4" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <div class="flex-grow-1">{{ session('exito') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if (session('error'))
    <div class="alerta pastel-rosa alert alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div class="flex-grow-1">{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alerta pastel-rosa mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>
            <div>Revisa los datos del formulario:</div>
            <ul class="mb-0 ps-3 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
