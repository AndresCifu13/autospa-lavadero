<?php

namespace App\Models;

use App\Enums\TipoVehiculo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Servicio que ofrece el lavadero (lavado general, polichado, etc.)
 * con un precio distinto según el tipo de vehículo.
 */
class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'precio_auto', 'precio_camioneta', 'precio_moto',
        'duracion_minutos', 'activo',
    ];

    protected function casts(): array
    {
        return [
            'precio_auto' => 'float',
            'precio_camioneta' => 'float',
            'precio_moto' => 'float',
            'duracion_minutos' => 'integer',
            'activo' => 'boolean',
        ];
    }

    /** Precio del servicio para un tipo de vehículo (HU-07). */
    public function precioPara(TipoVehiculo $tipo): float
    {
        return (float) $this->{$tipo->columnaPrecio()};
    }

    public function lavados(): BelongsToMany
    {
        return $this->belongsToMany(Lavado::class)->withPivot('precio')->withTimestamps();
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
