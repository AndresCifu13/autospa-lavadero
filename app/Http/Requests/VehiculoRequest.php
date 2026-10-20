<?php

namespace App\Http\Requests;

use App\Enums\TipoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehiculoRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('vehiculo')?->id;

        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'placa' => ['required', 'string', 'min:5', 'max:10', Rule::unique('vehiculos')->ignore($id)],
            'tipo' => ['required', Rule::enum(TipoVehiculo::class)],
            'marca' => ['nullable', 'string', 'max:50'],
            'modelo' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],
        ];
    }

    /** Limpiamos la placa ANTES de validar para que "abc 123" y "ABC123" sean la misma. */
    protected function prepareForValidation(): void
    {
        if ($this->placa) {
            $this->merge(['placa' => Vehiculo::limpiarPlaca($this->placa)]);
        }
    }
}
