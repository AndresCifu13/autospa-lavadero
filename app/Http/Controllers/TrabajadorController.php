<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use App\Http\Requests\TrabajadorRequest;
use App\Models\User;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CRUD de trabajadores. Solo lo usa el administrador.
 * HU-01 ver lista, HU-02 agregar, HU-03 editar, HU-04 eliminar
 */
class TrabajadorController extends Controller
{
    public function __construct(private UsuarioService $usuarios) {}

    public function index(Request $request): View
    {
        $trabajadores = User::buscar($request->buscar)
            ->when($request->rol, fn ($q, $rol) => $q->where('rol', $rol))
            ->withCount(['lavadosAsignados as lavados_hoy' => fn ($q) => $q->delDia()])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('trabajadores.index', ['trabajadores' => $trabajadores, 'roles' => Rol::opciones()]);
    }

    public function create(): View
    {
        return view('trabajadores.form', ['trabajador' => new User(['activo' => true, 'rol' => Rol::Empleado]), 'roles' => Rol::opciones()]);
    }

    public function store(TrabajadorRequest $request): RedirectResponse
    {
        $trabajador = $this->usuarios->crear($request->validated());

        return redirect()->route('trabajadores.index')
            ->with('exito', "Trabajador {$trabajador->name} creado. Se le enviaron sus datos de acceso por correo.");
    }

    public function edit(User $trabajador): View
    {
        return view('trabajadores.form', ['trabajador' => $trabajador, 'roles' => Rol::opciones()]);
    }

    public function update(TrabajadorRequest $request, User $trabajador): RedirectResponse
    {
        // Evita que el admin se quite a sí mismo el rol o se desactive por error
        if ($trabajador->is($request->user()) && ($request->rol !== Rol::Admin->value || ! $request->boolean('activo'))) {
            return back()->withInput()->with('error', 'No puedes quitarte el rol de administrador ni desactivarte a ti mismo.');
        }

        $this->usuarios->actualizar($trabajador, $request->validated());

        return redirect()->route('trabajadores.index')->with('exito', 'Datos del trabajador actualizados.');
    }

    public function destroy(Request $request, User $trabajador): RedirectResponse
    {
        if ($trabajador->is($request->user())) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $trabajador->delete(); // SoftDelete: sus lavados siguen apareciendo en los reportes

        return redirect()->route('trabajadores.index')->with('exito', "Se eliminó a {$trabajador->name}.");
    }
}
