<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Cada usuario puede cambiar sus propios datos y su contraseña. */
class PerfilController extends Controller
{
    public function edit(Request $request): View
    {
        return view('perfil.edit', ['usuario' => $request->user()]);
    }

    public function update(PerfilRequest $request): RedirectResponse
    {
        $datos = $request->safe()->only('name', 'telefono', 'email');

        if ($request->filled('password')) {
            $datos['password'] = $request->validated('password');
        }

        $request->user()->update($datos);

        return back()->with('exito', 'Tu perfil se actualizó.');
    }
}
