<?php

namespace App\Services;

class TicketModelHider
{
    public static function hideTicketFields($ticket)
    {
        // Para ocultar los PKs, FKs algunos campos y timestamps de las tablas relaciones
        $ticket->usuario?->makeHidden(["id", "telefono", "email", "created_at", "updated_at"]);
        $ticket->usuario?->departamento?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "created_at", "updated_at"]);
        $ticket->usuario?->departamento?->area?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
        $ticket->categoriaTicket?->makeHidden(["id","created_at", "updated_at"]);
        $ticket->estadoTicket?->makeHidden(["id", "created_at", "updated_at"]);
        $ticket->prioridadTicket?->makeHidden(["id", "created_at", "updated_at"]);
        return $ticket;
    }
}
