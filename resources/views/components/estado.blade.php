{{-- Etiqueta de color según el estado del lavado --}}
@props(['estado'])
<span class="etiqueta {{ $estado->color() }}">{{ $estado->etiqueta() }}</span>
