{{-- Formulario para enviar el reporte por correo (HU-16) --}}
<form method="POST" action="{{ route('reportes.enviar') }}" class="tarjeta tarjeta-cuerpo mb-3 d-flex flex-wrap gap-2 align-items-center no-imprimir">
    @csrf
    <input type="hidden" name="tipo" value="{{ $tipo }}">
    <input type="hidden" name="fecha" value="{{ $valorFecha }}">
    <i class="bi bi-envelope-paper fs-4 text-suave"></i>
    <input type="text" name="correo" value="{{ old('correo', $correo) }}" @class(['form-control flex-grow-1', 'is-invalid' => $errors->has('correo')]) style="max-width:420px"
           placeholder="uno@gmail.com, otro@hotmail.com" title="Puedes poner varios correos separados por coma" autocomplete="off" required>
    <button class="btn btn-principal"><i class="bi bi-send me-1"></i> Enviar por correo</button>
    @error('correo')<div class="w-100 text-danger small">{{ $message }}</div>@enderror
</form>
