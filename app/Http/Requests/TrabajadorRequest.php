<?php

namespace App\Http\Requests;

use App\Enums\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Validación para crear y editar trabajadores (HU-02, HU-03). */
class TrabajadorRequest extends FormRequest
{
    public function rules(): array
    {
        // Al editar, ignoramos al mismo usuario en las reglas "unique"
        $id = $this->route('trabajador')?->id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'documento' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($id)],
            'rol' => ['required', Rule::enum(Rol::class)],
            'activo' => ['boolean'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo')]);
    }

    public function attributes(): array
    {
        return ['name' => 'nombre'];
    }
}
