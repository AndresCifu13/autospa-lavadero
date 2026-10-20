@extends('layouts.app')
@section('titulo', 'Gastos')

@section('contenido')
<x-encabezado titulo="Gastos" icono="bi-wallet2" subtitulo="Lo que se gasta en el lavadero">
    <a href="{{ route('gastos.create') }}" class="btn btn-principal"><i class="bi bi-plus-lg me-1"></i> Registrar gasto</a>
</x-encabezado>

<form class="tarjeta tarjeta-cuerpo mb-3 row g-2 align-items-end mx-0" method="GET">
    <div class="col-6 col-md-3"><label class="form-label small" for="desde">Desde</label><input type="date" id="desde" name="desde" value="{{ $desde->toDateString() }}" class="form-control"></div>
    <div class="col-6 col-md-3"><label class="form-label small" for="hasta">Hasta</label><input type="date" id="hasta" name="hasta" value="{{ $hasta->toDateString() }}" class="form-control"></div>
    <div class="col-8 col-md-4">
        <label class="form-label small" for="categoria">Categoría</label>
        <select id="categoria" name="categoria" class="form-select">
            <option value="">Todas</option>
            @foreach ($categorias as $valor => $texto)<option value="{{ $valor }}" @selected(request('categoria') === $valor)>{{ $texto }}</option>@endforeach
        </select>
    </div>
    <div class="col-4 col-md-2"><button class="btn btn-suave w-100"><i class="bi bi-funnel"></i></button></div>
</form>

<div class="stat pastel-rosa mb-3 d-flex justify-content-between align-items-center">
    <span class="stat-texto">Total del periodo</span>
    <span class="stat-valor m-0">{{ dinero($total) }}</span>
</div>

<div class="tarjeta overflow-hidden">
    @if ($gastos->isEmpty())
        <x-vacio icono="bi-wallet2" texto="No hay gastos en este periodo." />
    @else
        <div class="table-responsive">
            <table class="table tabla">
                <thead><tr><th>Fecha</th><th>Descripción</th><th class="d-none d-md-table-cell">Categoría</th><th class="text-end">Monto</th><th class="d-none d-lg-table-cell">Registró</th><th></th></tr></thead>
                <tbody>
                @foreach ($gastos as $gasto)
                    <tr>
                        <td class="small text-nowrap">{{ $gasto->fecha->format('d/m/Y') }}</td>
                        <td>{{ $gasto->descripcion }}</td>
                        <td class="d-none d-md-table-cell"><span class="etiqueta pastel-durazno">{{ $gasto->categoria->etiqueta() }}</span></td>
                        <td class="text-end fw-bold">{{ dinero($gasto->monto) }}</td>
                        <td class="small text-suave d-none d-lg-table-cell">{{ $gasto->usuario->name }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('gastos.edit', $gasto) }}" class="btn btn-sm btn-suave"><i class="bi bi-pencil"></i></a>
                            <x-boton-eliminar :ruta="route('gastos.destroy', $gasto)" mensaje="Se eliminará el gasto «{{ $gasto->descripcion }}»." />
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
<div class="mt-3">{{ $gastos->links() }}</div>
@endsection
