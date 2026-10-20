{{--
    Campo de formulario con su etiqueta y su mensaje de error.
    Uso: <x-campo nombre="email" etiqueta="Correo" tipo="email" :valor="$usuario->email" />
--}}
@props(['nombre', 'etiqueta', 'tipo' => 'text', 'valor' => null, 'ayuda' => null, 'requerido' => false])

<div {{ $attributes->only('class')->merge(['class' => 'mb-3']) }}>
    <label for="{{ $nombre }}" class="form-label">
        {{ $etiqueta }} @if ($requerido)<span class="text-danger">*</span>@endif
    </label>
    <input type="{{ $tipo }}" id="{{ $nombre }}" name="{{ $nombre }}"
           value="{{ $tipo === 'password' ? '' : old($nombre, $valor) }}"
           @class(['form-control', 'is-invalid' => $errors->has($nombre)])
           @required($requerido)
           {{ $attributes->except('class') }}>
    @error($nombre)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($ayuda)
        <div class="form-text">{{ $ayuda }}</div>
    @endif
</div>
