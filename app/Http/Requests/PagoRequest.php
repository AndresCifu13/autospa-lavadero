<?php

namespace App\Http\Requests;

use App\Enums\MetodoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PagoRequest extends FormRequest
{
    public function rules(): array
    {
        return ['metodo_pago' => ['required', Rule::enum(MetodoPago::class)]];
    }

    public function metodo(): MetodoPago
    {
        return MetodoPago::from($this->validated('metodo_pago'));
    }
}
