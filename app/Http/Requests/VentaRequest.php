<?php

namespace App\Http\Requests;

use App\Enums\MetodoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VentaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'metodo_pago' => ['required', Rule::enum(MetodoPago::class)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    /** Quitamos las filas vacías del carrito antes de validar. */
    protected function prepareForValidation(): void
    {
        $items = collect($this->input('items', []))
            ->filter(fn ($i) => ! empty($i['producto_id']) && (int) ($i['cantidad'] ?? 0) > 0)
            ->values()->all();

        $this->merge(['items' => $items]);
    }

    public function messages(): array
    {
        return ['items.required' => 'Agrega al menos un producto a la venta.'];
    }
}
