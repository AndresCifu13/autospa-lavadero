<?php

namespace App\Http\Requests;

use App\Enums\CategoriaGasto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GastoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'descripcion' => ['required', 'string', 'max:150'],
            'categoria' => ['required', Rule::enum(CategoriaGasto::class)],
            'monto' => ['required', 'numeric', 'min:1'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
