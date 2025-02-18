<?php

namespace App\Services;

class BitacoraTicketModelHider
{
    public static function hideBitacoraTicketFields($log)
    {
        // Para ocultar los PKs, algunos campos y timestamps de la tabla relacionada con usuarios
        $log->tecnicoAsignado?->makeHidden(["id", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->usuario?->makeHidden(["id", "telefono", "email", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->usuario?->departamento?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->usuario?->departamento?->area?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
        // Para ocultar los PKs, algunos campos y timestamps de la tabla relacionada con tickets
        $log->tecnicoAsignado?->ticket?->makeHidden(["id", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->ticket?->usuario?->makeHidden(["id", "telefono", "email", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->ticket?->usuario?->departamento?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->ticket?->usuario?->departamento?->area?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->ticket?->categoriaTicket?->makeHidden(["id", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->ticket?->estadoTicket?->makeHidden(["id", "color_estado", "orden_prioridad", "created_at", "updated_at"]);
        $log->tecnicoAsignado?->ticket?->prioridadTicket?->makeHidden(["id", "color_prioridad", "orden_prioridad", "created_at", "updated_at"]);
        return $log;
    }
}
