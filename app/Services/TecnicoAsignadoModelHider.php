<?php

namespace App\Services;

class TecnicoAsignadoModelHider
{
    public static function hideTecnicoAsignadoFields($technician)
    {
        // Para ocultar los PKs, algunos campos y timestamps de la tabla relacionada con usuarios
        $technician->usuario?->makeHidden(["id", "telefono", "email", "created_at", "updated_at"]);
        $technician->usuario?->departamento?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "created_at", "updated_at"]);
        $technician->usuario?->departamento?->area?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
        // Para ocultar los PKs, algunos campos y timestamps de la tabla relacionada con tickets
        $technician->ticket?->makeHidden(["id", "updated_at"]);
        $technician->ticket?->usuario?->makeHidden(["id", "telefono", "email", "created_at", "updated_at"]);
        $technician->ticket?->usuario?->departamento?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "created_at", "updated_at"]);
        $technician->ticket?->usuario?->departamento?->area?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
        $technician->ticket?->categoriaTicket?->makeHidden(["id","created_at", "updated_at"]);
        $technician->ticket?->estadoTicket?->makeHidden(["id", "color_estado", "orden_prioridad", "created_at", "updated_at"]);
        $technician->ticket?->prioridadTicket?->makeHidden(["id", "color_prioridad", "orden_prioridad", "created_at", "updated_at"]);
        return $technician;
    }
}
