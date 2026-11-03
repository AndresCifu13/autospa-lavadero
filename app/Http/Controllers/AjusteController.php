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
            'correosAvisos' => Ajuste::obtener(Ajuste::CORREOS_AVISOS),
            'avisosActivos' => Ajuste::avisosActivos(),
        ]);
    }

    public function update(AjusteRequest $request): RedirectResponse
    {
        foreach ($request->ajustes() as $clave => $valor) {
            Ajuste::guardar($clave, $valor);
        }

        return back()->with('exito', 'Configuración guardada.');
    }
}
