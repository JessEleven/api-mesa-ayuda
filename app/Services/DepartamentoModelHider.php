<?php

namespace App\Services;

class DepartamentoModelHider
{
    public static function hideDepartamentoFields($department)
    {
        // Para ocultar el PK y timestamps de la tabla areas
        $department->area?->makeHidden(["id", "created_at", "updated_at"]);
        return $department;
    }
}
