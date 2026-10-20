<?php

namespace App\Models;

use App\Enums\EstadoLavado;
use App\Enums\MetodoPago;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Un lavado es cada ingreso de un vehículo al lavadero:
 * qué servicios se le hacen, quién lo lava, cuánto cuesta y si ya pagó.
 */
class Lavado extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehiculo_id', 'cliente_id', 'empleado_id', 'registrado_por', 'estado',
        'subtotal', 'descuento', 'total', 'es_gratis', 'pagado', 'metodo_pago',
        'pagado_en', 'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'estado' => EstadoLavado::class,
            'metodo_pago' => MetodoPago::class,
            'subtotal' => 'float',
            'descuento' => 'float',
            'total' => 'float',
            'es_gratis' => 'boolean',
            'pagado' => 'boolean',
            'pagado_en' => 'datetime',
        ];
    }

    // ---------- Relaciones ----------

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class)->withTrashed();
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class)->withTrashed();
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'empleado_id')->withTrashed();
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por')->withTrashed();
    }

    public function servicios(): BelongsToMany
    {
        return $this->belongsToMany(Servicio::class)->withPivot('precio')->withTimestamps();
    }

    // ---------- Filtros (scopes) ----------

    /** Lavados de un día (por defecto hoy). HU-10 */
    public function scopeDelDia(Builder $query, ?Carbon $fecha = null): Builder
    {
        $fecha ??= now();

        return $query->whereBetween('created_at', [$fecha->copy()->startOfDay(), $fecha->copy()->endOfDay()]);
    }

    public function scopePagados(Builder $query): Builder
    {
        return $query->where('pagado', true);
    }

    public function scopeDeEmpleado(Builder $query, User $empleado): Builder
    {
        return $query->where('empleado_id', $empleado->id);
    }

    // ---------- Ayudas ----------

    public function puedeAvanzar(): bool
    {
        return $this->estado->siguiente() !== null;
    }

    /** Número bonito para el ticket: #000123 */
    public function codigo(): string
    {
        return '#'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
