<?php

namespace App\Models;

use App\Enums\CategoriaGasto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gasto del lavadero (compra de insumos, pagos, servicios...). HU-14
 */
class Gasto extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion', 'categoria', 'monto', 'fecha', 'user_id'];

    protected function casts(): array
    {
        return [
            'categoria' => CategoriaGasto::class,
            'monto' => 'float',
            'fecha' => 'date',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}
