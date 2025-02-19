<?php

namespace App\Helpers;

use App\Models\Departamento;

class SecuenciaDepartamentoHelper
{
    // Para generar el campo secuencia_departamento (ej. D-001)
    public static function generateSequenceDepartment($areaId, $siglaDepartment)
    {
        // Se obtiene la última secuencia del departamento por área
        $lastSequence = Departamento::where("id_area", $areaId)
            ->orderBy("id", "desc")
            ->value("secuencia_departamento");

        $sequenceNumber = $lastSequence
            // Extrae el número secuencial global
            ? (int) substr($lastSequence, 0, 3) + 1
            // Si no existe se inicializa en 1 la secuencia
            : 1;

        // Se formatea la secuencia a 3 dígitos y se concatena con la sigla
        $newSequence = sprintf("%03d-%s", $sequenceNumber, $siglaDepartment);

        return $newSequence;
    }

    // Para actualizar el campo secuencia_area cuando la sigla cambia (ej. DA-001)
    public static function updateSequenceDepartment($department, $newSiglaDerpatment) {
        // Si ha cambiado, se conserva la secuencia y se actualiza solo la sigla
        $currentSequence = $department->secuencia_departamento;
        // Se extrae la parte numérica de la secuencia actual
        $sequenceNumber = substr($currentSequence, 0, 3);
        // Se genera la nueva secuencia concatenando la nueva sigla
        $updateSequence = sprintf("%03d-%s", $sequenceNumber, $newSiglaDerpatment);

        return $updateSequence;
    }
}
