<?php

namespace App\Models;

use App\Enums\TipoVehiculo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Vehículo de un cliente (se identifica por la placa).
 */
class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['cliente_id', 'placa', 'tipo', 'marca', 'modelo', 'color'];

    protected function casts(): array
    {
        return ['tipo' => TipoVehiculo::class];
    }

    /** La placa siempre se guarda en mayúsculas y sin espacios (ABC123). */
    protected function placa(): Attribute
    {
        return Attribute::make(
            set: fn (string $valor) => self::limpiarPlaca($valor),
        );
    }

    public static function limpiarPlaca(string $placa): string
    {
        return mb_strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $placa));
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function lavados(): HasMany
    {
        return $this->hasMany(Lavado::class);
    }

    /** Texto corto para mostrar: "Mazda 3 · Rojo" */
    public function descripcion(): string
    {
        return collect([$this->marca, $this->modelo])->filter()->implode(' ')
            .($this->color ? ' · '.$this->color : '');
    }
}
