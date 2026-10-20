<?php

namespace App\Models;

use App\Enums\MetodoPago;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Venta de la tienda (puede tener varios productos).
 */
class Venta extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id', 'user_id', 'total', 'metodo_pago'];

    protected function casts(): array
    {
        return [
            'total' => 'float',
            'metodo_pago' => MetodoPago::class,
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function scopeDelDia(Builder $query, ?Carbon $fecha = null): Builder
    {
        $fecha ??= now();

        return $query->whereBetween('created_at', [$fecha->copy()->startOfDay(), $fecha->copy()->endOfDay()]);
    }

    public function codigo(): string
    {
        return 'V-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
