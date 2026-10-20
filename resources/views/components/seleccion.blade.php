{{--
    Lista desplegable con etiqueta y error.
    Uso: <x-seleccion nombre="rol" etiqueta="Rol" :opciones="$roles" :valor="$trabajador->rol?->value" />
--}}
@props(['nombre', 'etiqueta', 'opciones' => [], 'valor' => null, 'vacio' => null, 'requerido' => false])

<div {{ $attributes->only('class')->merge(['class' => 'mb-3']) }}>
    <label for="{{ $nombre }}" class="form-label">
        {{ $etiqueta }} @if ($requerido)<span class="text-danger">*</span>@endif
    </label>
    <select id="{{ $nombre }}" name="{{ $nombre }}" @class(['form-select', 'is-invalid' => $errors->has($nombre)]) @required($requerido) {{ $attributes->except('class') }}>
        @if ($vacio)
            <option value="">{{ $vacio }}</option>
        @endif
        @foreach ($opciones as $clave => $texto)
            <option value="{{ $clave }}" @selected((string) old($nombre, $valor) === (string) $clave)>{{ $texto }}</option>
        @endforeach
    </select>
    @error($nombre)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
