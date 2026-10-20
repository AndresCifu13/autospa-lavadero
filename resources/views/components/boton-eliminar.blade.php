{{-- Botón de eliminar que pide confirmación. Uso: <x-boton-eliminar :ruta="route('x.destroy', $x)" mensaje="..." /> --}}
@props(['ruta', 'mensaje' => 'Esta acción no se puede deshacer.', 'texto' => null])

<form method="POST" action="{{ $ruta }}" data-confirmar="{{ $mensaje }}" class="d-inline">
    @csrf
    @method('DELETE')
    <button type="submit" {{ $attributes->class(['btn btn-borrar', 'btn-sm' => ! $texto]) }} title="Eliminar">
        <i class="bi bi-trash3"></i>@if ($texto) {{ $texto }}@endif
    </button>
</form>
