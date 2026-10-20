<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('cliente')?->id;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'documento' => ['nullable', 'string', 'max:20', Rule::unique('clientes')->ignore($id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
        ];
    }
}
