<?php

namespace App\Http\Requests;

use App\Models\Ajuste;
use App\Rules\ListaDeCorreos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AjusteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nombre_negocio' => ['required', 'string', 'max:100'],
            'lavadas_para_gratis' => ['required', 'integer', 'min:1', 'max:50'],
            // Uno o varios correos separados por coma
            'correo_reportes' => ['nullable', 'string', 'max:255', new ListaDeCorreos],
            'correos_avisos' => ['nullable', 'string', 'max:255', new ListaDeCorreos],
            'avisos_tipos' => ['nullable', 'array'],
            'avisos_tipos.*' => [Rule::in(array_keys(Ajuste::TIPOS_AVISO))],
        ];
    }

    public function attributes(): array
    {
        return [
            'correo_reportes' => 'correos para reportes',
            'correos_avisos' => 'correos para avisos',
        ];
    }

    /** Datos listos para guardar: correos ordenados y los tipos como "lavados,ventas". */
    public function ajustes(): array
    {
        $datos = $this->validated();

        foreach (['correo_reportes', 'correos_avisos'] as $campo) {
            $datos[$campo] = implode(', ', Ajuste::listaCorreos($datos[$campo] ?? null)) ?: null;
        }

        // Si no se marca ninguno se guarda "-" (ninguno). Vacío significaría "todos".
        $datos['avisos_tipos'] = implode(',', $datos['avisos_tipos'] ?? []) ?: '-';

        return $datos;
    }
}
