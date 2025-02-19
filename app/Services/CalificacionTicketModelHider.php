<?php

namespace App\Services;

class CalificacionTicketModelHider
{
    public static function hideCalificacionTicketFields($qualification)
    {
        // Para ocultar los PKs, algunos campos y timestamps de las tablas relaciones
        $qualification->ticket?->makeHidden(["id", "recurso_eliminado", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at", "updated_at"]);
        $qualification->ticket?->usuario?->makeHidden(["id", "telefono", "email", "id_departamento", "created_at", "updated_at"]);
        $qualification->ticket?->usuario?->departamento?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
        $qualification->ticket?->usuario?->departamento?->area?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
        $qualification->ticket?->categoriaTicket?->makeHidden(["id", "created_at", "updated_at"]);
        $qualification->ticket?->estadoTicket?->makeHidden(["id", "color_estado", "orden_prioridad", "created_at", "updated_at"]);
        $qualification->ticket?->prioridadTicket?->makeHidden(["id", "color_prioridad", "orden_prioridad", "created_at", "updated_at"]);
        return $qualification;
    }
}
