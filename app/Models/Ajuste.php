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
    public const CORREOS_AVISOS = 'correos_avisos';
    public const AVISOS_TIPOS = 'avisos_tipos';

    public const MAXIMO_CORREOS = 10;

    /** Qué registros pueden generar un aviso por correo (clave => texto). */
    public const TIPOS_AVISO = [
        'lavados' => 'Lavados',
        'ventas' => 'Ventas de la tienda',
        'clientes' => 'Clientes',
        'vehiculos' => 'Vehículos',
        'gastos' => 'Gastos',
        'productos' => 'Productos',
        'trabajadores' => 'Trabajadores',
    ];

    /**
     * Convierte "uno@x.com, otro@y.com" en ['uno@x.com', 'otro@y.com'].
     * Ignora lo que no sea un correo válido y los repetidos.
     */
    public static function listaCorreos(?string $texto): array
    {
        $partes = preg_split('/[\s,;]+/', (string) $texto, -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_unique(array_filter(
            array_map('mb_strtolower', $partes),
            fn ($correo) => filter_var($correo, FILTER_VALIDATE_EMAIL),
        )));
    }

    /** Tipos de registro marcados en Configuración (por defecto todos). */
    public static function avisosActivos(): array
    {
        $guardado = static::obtener(self::AVISOS_TIPOS);

        return $guardado === null
            ? array_keys(self::TIPOS_AVISO)
            : array_values(array_intersect(explode(',', $guardado), array_keys(self::TIPOS_AVISO)));
    }

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
