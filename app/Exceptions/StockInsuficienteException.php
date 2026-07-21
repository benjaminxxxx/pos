<?php

namespace App\Exceptions;

class StockInsuficienteException extends \RuntimeException
{
    public function __construct(
        public readonly int $productoId,
        public readonly int $sucursalId,
        public readonly float $disponible,
        public readonly float $solicitado,
    ) {
        parent::__construct(
            "Stock insuficiente (producto {$productoId}, sucursal {$sucursalId}): "
            . "disponible {$disponible}, solicitado {$solicitado}"
        );
    }
}