<?php

namespace App\Helpers;

use App\Models\Area;

class SecuenciaAreaHelper
{
    // Para generar el campo secuencia_area (ej. F-001)
    public static function generateSequenceArea($siglaArea)
    {
        // Se obtiene la última secuencia global
        $lastSequence = Area::orderBy("id", "desc")
            ->value("secuencia_area");

        $sequenceNumber = $lastSequence
            // Se extrae y aumenta el número de la secuencia
            ? (int) substr($lastSequence, 0, 3) + 1
            // Si no existe se inicializa en 1 la secuencia
            : 1;

        // Se formatea la secuencia a 3 dígitos y se concatena con la sigla
        $newSequence = sprintf("%03d-%s", $sequenceNumber, $siglaArea);

        return $newSequence;
    }

    // Para actualizar el campo secuencia_area cuando la sigla cambia (ej. FA-001)
    public static function updateSequenceArea($area, $newSiglaArea) {
        // Si ha cambiado, se conserva la secuencia y se actualiza solo la sigla
        $currentSequence = $area->secuencia_area;
        // Se extrae la parte numérica de la secuencia actual
        $sequenceNumber = substr($currentSequence, 0, 3);

        // Se genera la nueva secuencia concatenando la nueva sigla
        $updateSequence = sprintf('%03d-%s', $sequenceNumber, $newSiglaArea);

        return $updateSequence;
    }
}
