<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Configuraciones del negocio guardadas en la base de datos (clave => valor).
 * Ejemplo: Ajuste::obtener('lavadas_para_gratis', 10)
 */
class Ajuste extends Model
{
    protected $fillable = ['clave', 'valor'];

    // Claves que usa el sistema (así evitamos escribir mal los nombres)
    public const LAVADAS_PARA_GRATIS = 'lavadas_para_gratis';
    public const CORREO_REPORTES = 'correo_reportes';
    public const NOMBRE_NEGOCIO = 'nombre_negocio';

    public static function obtener(string $clave, mixed $porDefecto = null): mixed
    {
        $valor = Cache::rememberForever("ajuste.{$clave}", fn () => static::where('clave', $clave)->value('valor'));

        return $valor ?? $porDefecto;
    }

    public static function guardar(string $clave, mixed $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        Cache::forget("ajuste.{$clave}");
    }

    /** Cuántas lavadas pagadas necesita un cliente para ganar una gratis. */
    public static function lavadasParaGratis(): int
    {
        return max(1, (int) static::obtener(self::LAVADAS_PARA_GRATIS, 10));
    }
}
