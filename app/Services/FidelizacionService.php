<?php

namespace App\Services;

use App\Exceptions\LavadoException;
use App\Models\Ajuste;
use App\Models\Cliente;
use App\Notifications\LavadaGratisGanada;

/**
 * Tarjeta de fidelización:
 * cada lavada PAGADA suma 1. Al llegar a la meta (ej: 10) el cliente gana una gratis
 * y el contador vuelve a empezar.
 */
class FidelizacionService
{
    public function __construct(private NotificadorService $notificador) {}

    public function meta(): int
    {
        return Ajuste::lavadasParaGratis();
    }

    /**
     * Suma una lavada al cliente. Devuelve true si con esta se ganó una gratis.
     */
    public function sumarLavada(Cliente $cliente): bool
    {
        $cliente->lavadas_acumuladas++;
        $gano = false;

        if ($cliente->lavadas_acumuladas >= $this->meta()) {
            $cliente->lavadas_acumuladas -= $this->meta();
            $cliente->lavadas_gratis++;
            $gano = true;
        }

        $cliente->save();

        if ($gano) {
            $this->notificador->enviar($cliente, new LavadaGratisGanada($cliente));
        }

        return $gano;
    }

    /** Quita una lavada (se usa cuando se elimina un lavado que ya estaba pagado). */
    public function restarLavada(Cliente $cliente): void
    {
        if ($cliente->lavadas_acumuladas > 0) {
            $cliente->lavadas_acumuladas--;
        } elseif ($cliente->lavadas_gratis > 0) {
            // Esa lavada fue la que completó la tarjeta: devolvemos la gratis
            $cliente->lavadas_gratis--;
            $cliente->lavadas_acumuladas = $this->meta() - 1;
        }

        $cliente->save();
    }

    /** Gasta una lavada gratis del cliente. */
    public function usarLavadaGratis(Cliente $cliente): void
    {
        if (! $cliente->tieneLavadaGratis()) {
            throw LavadoException::sinLavadaGratis();
        }

        $cliente->decrement('lavadas_gratis');
    }

    /** Devuelve una lavada gratis (si se elimina un lavado que era gratis). */
    public function devolverLavadaGratis(Cliente $cliente): void
    {
        $cliente->increment('lavadas_gratis');
    }

    /**
     * Datos para dibujar la barrita de progreso del cliente.
     *
     * @return array{acumuladas:int, meta:int, faltan:int, porcentaje:int, gratis:int}
     */
    public function progreso(Cliente $cliente): array
    {
        $meta = $this->meta();

        return [
            'acumuladas' => $cliente->lavadas_acumuladas,
            'meta' => $meta,
            'faltan' => max(0, $meta - $cliente->lavadas_acumuladas),
            'porcentaje' => (int) min(100, round($cliente->lavadas_acumuladas * 100 / $meta)),
            'gratis' => $cliente->lavadas_gratis,
        ];
    }
}
