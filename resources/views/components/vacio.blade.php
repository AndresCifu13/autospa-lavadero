{{-- Mensaje cuando una lista no tiene datos --}}
@props(['icono' => 'bi-inbox', 'texto' => 'No hay registros todavía.'])
<div class="text-center py-5 text-suave">
    <i class="bi {{ $icono }} d-block mb-2" style="font-size:2.4rem;opacity:.5"></i>
    <p class="mb-2 fw-semibold">{{ $texto }}</p>
    {{ $slot }}
</div>
