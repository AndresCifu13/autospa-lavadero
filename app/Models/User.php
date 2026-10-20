<?php

namespace App\Models;

use App\Enums\Rol;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario del sistema: puede ser Administrador o Empleado (trabajador del lavadero).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'documento', 'telefono', 'email', 'password', 'rol', 'activo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'rol' => Rol::class,
            'activo' => 'boolean',
        ];
    }

    // ---------- Relaciones ----------

    /** Lavados que este empleado tiene asignados. */
    public function lavadosAsignados(): HasMany
    {
        return $this->hasMany(Lavado::class, 'empleado_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    // ---------- Ayudas ----------

    public function esAdmin(): bool
    {
        return $this->rol === Rol::Admin;
    }

    public function esEmpleado(): bool
    {
        return $this->rol === Rol::Empleado;
    }

    /** Iniciales para el avatar redondo del menú (ej: "Germán Cabrera" => "GC"). */
    public function iniciales(): string
    {
        $partes = preg_split('/\s+/', trim($this->name));
        $letras = mb_substr($partes[0] ?? '', 0, 1).mb_substr($partes[1] ?? '', 0, 1);

        return mb_strtoupper($letras);
    }

    // ---------- Filtros (scopes) ----------

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('rol', Rol::Admin->value);
    }

    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (! $texto) {
            return $query;
        }

        return $query->where(function ($q) use ($texto) {
            $q->where('name', 'like', "%{$texto}%")
                ->orWhere('email', 'like', "%{$texto}%")
                ->orWhere('documento', 'like', "%{$texto}%");
        });
    }
}
