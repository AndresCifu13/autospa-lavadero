{{-- Título de cada página con espacio para botones a la derecha --}}
@props(['titulo', 'subtitulo' => null, 'icono' => null])

<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="titulo-pagina">
            @if ($icono)<i class="bi {{ $icono }} me-1"></i>@endif {{ $titulo }}
        </h1>
        @if ($subtitulo)
            <p class="text-suave mb-0">{{ $subtitulo }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="d-flex flex-wrap gap-2">{{ $slot }}</div>
    @endif
</div>
