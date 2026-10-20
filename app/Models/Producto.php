<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Producto que se vende en la tienda del lavadero.
 */
class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre', 'categoria', 'descripcion', 'precio', 'stock', 'stock_minimo', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'float',
            'stock' => 'integer',
            'stock_minimo' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function stockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeConStockBajo(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (! $texto) {
            return $query;
        }

        return $query->where(fn ($q) => $q->where('nombre', 'like', "%{$texto}%")
            ->orWhere('categoria', 'like', "%{$texto}%"));
    }
}
