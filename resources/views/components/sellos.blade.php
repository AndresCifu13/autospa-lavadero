{{-- Tarjeta de fidelización: un círculo por cada lavada --}}
@props(['progreso'])
<div class="sellos" aria-label="{{ $progreso['acumuladas'] }} de {{ $progreso['meta'] }} lavadas">
    @for ($i = 1; $i <= $progreso['meta']; $i++)
        @if ($i <= $progreso['acumuladas'])
            <span class="sello lleno"><i class="bi bi-droplet-fill"></i></span>
        @elseif ($i === $progreso['meta'])
            <span class="sello premio"><i class="bi bi-gift"></i></span>
        @else
            <span class="sello">{{ $i }}</span>
        @endif
    @endfor
</div>
