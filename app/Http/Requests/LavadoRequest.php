<?php

namespace App\Http\Requests;

use App\Enums\Rol;
use App\Enums\TipoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Registro de un vehículo que llega al lavadero (HU-06).
 * Si la placa ya existe no se piden los datos del cliente.
 */
class LavadoRequest extends FormRequest
{
    public function rules(): array
    {
        $placaExiste = $this->placa && Vehiculo::where('placa', $this->placa)->exists();
        $necesitaCliente = ! $placaExiste && ! $this->cliente_id;

        return [
            'placa' => ['required', 'string', 'min:5', 'max:10'],
            'tipo' => ['required', Rule::enum(TipoVehiculo::class)],
            'marca' => ['nullable', 'string', 'max:50'],
            'modelo' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],

            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'cliente_nombre' => [Rule::requiredIf($necesitaCliente), 'nullable', 'string', 'max:100'],
            'cliente_documento' => ['nullable', 'string', 'max:20'],
            'cliente_telefono' => ['nullable', 'string', 'max:20'],
            'cliente_email' => ['nullable', 'email', 'max:150'],

            'servicios' => ['required', 'array', 'min:1'],
            'servicios.*' => ['integer', 'exists:servicios,id'],
            'empleado_id' => ['nullable', Rule::exists('users', 'id')->where('rol', Rol::Empleado->value)->where('activo', true)],
            'usar_gratis' => ['boolean'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'placa' => $this->placa ? Vehiculo::limpiarPlaca($this->placa) : null,
            'usar_gratis' => $this->boolean('usar_gratis'),
        ]);
    }

    public function attributes(): array
    {
        return [
            'cliente_nombre' => 'nombre del cliente',
            'cliente_email' => 'correo del cliente',
            'servicios' => 'servicios',
            'empleado_id' => 'empleado',
        ];
    }

    public function messages(): array
    {
        return [
            'servicios.required' => 'Elige al menos un servicio.',
            'cliente_nombre.required' => 'La placa es nueva: escribe el nombre del cliente.',
        ];
    }
}
