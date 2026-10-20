<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjusteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre_negocio' => ['required', 'string', 'max:100'],
            'lavadas_para_gratis' => ['required', 'integer', 'min:1', 'max:50'],
            'correo_reportes' => ['nullable', 'email'],
        ];
    }
}
