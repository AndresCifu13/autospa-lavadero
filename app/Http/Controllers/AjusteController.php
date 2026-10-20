<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjusteRequest;
use App\Models\Ajuste;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/** Configuración general del lavadero. Solo administrador. */
class AjusteController extends Controller
{
    public function edit(): View
    {
        return view('ajustes.edit', [
            'nombreNegocio' => Ajuste::obtener(Ajuste::NOMBRE_NEGOCIO, config('app.name')),
            'lavadasParaGratis' => Ajuste::lavadasParaGratis(),
            'correoReportes' => Ajuste::obtener(Ajuste::CORREO_REPORTES),
        ]);
    }

    public function update(AjusteRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $clave => $valor) {
            Ajuste::guardar($clave, $valor);
        }

        return back()->with('exito', 'Configuración guardada.');
    }
}
