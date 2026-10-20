<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServicioRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'precio_auto' => ['required', 'numeric', 'min:0'],
            'precio_camioneta' => ['required', 'numeric', 'min:0'],
            'precio_moto' => ['required', 'numeric', 'min:0'],
            'duracion_minutos' => ['required', 'integer', 'min:5', 'max:600'],
            'activo' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo')]);
    }

    public function attributes(): array
    {
        return [
            'precio_auto' => 'precio para automóvil',
            'precio_camioneta' => 'precio para camioneta',
            'precio_moto' => 'precio para moto',
            'duracion_minutos' => 'duración',
        ];
    }
}
