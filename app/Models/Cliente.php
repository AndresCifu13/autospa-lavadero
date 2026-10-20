<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * Cliente del lavadero. Puede tener varios vehículos
 * y va sumando lavadas para ganarse una gratis.
 */
class Cliente extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nombre', 'documento', 'telefono', 'email', 'lavadas_acumuladas', 'lavadas_gratis',
    ];

    protected function casts(): array
    {
        return [
            'lavadas_acumuladas' => 'integer',
            'lavadas_gratis' => 'integer',
        ];
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function lavados(): HasMany
    {
        return $this->hasMany(Lavado::class);
    }

    public function compras(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /** Laravel usa esto para saber a qué correo mandar las notificaciones. */
    public function routeNotificationForMail(): ?string
    {
        return $this->email;
    }

    public function tieneLavadaGratis(): bool
    {
        return $this->lavadas_gratis > 0;
    }

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (! $texto) {
            return $query;
        }

        return $query->where(function ($q) use ($texto) {
            $q->where('nombre', 'like', "%{$texto}%")
                ->orWhere('documento', 'like', "%{$texto}%")
                ->orWhere('telefono', 'like', "%{$texto}%")
                ->orWhereHas('vehiculos', fn ($v) => $v->where('placa', 'like', "%{$texto}%"));
        });
    }
}
