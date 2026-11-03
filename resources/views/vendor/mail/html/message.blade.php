<x-mail::layout>
{{-- Encabezado con la marca --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{ config('app.name') }}
</x-mail::header>
</x-slot:header>

{{-- Contenido --}}
{!! $slot !!}

{{-- Texto pequeño (enlace alternativo del botón) --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Pie --}}
<x-slot:footer>
<x-mail::footer>
{{ config('app.name') }} · Lavadero y tienda<br>
Este correo se envió automáticamente desde el sistema del lavadero.<br>
© {{ date('Y') }} {{ config('app.name') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
