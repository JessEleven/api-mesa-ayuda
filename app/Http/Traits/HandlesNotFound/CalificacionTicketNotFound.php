<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\CalificacionTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CalificacionTicketNotFound
{
    public function findCalificacionTicketOrFail($calificacionTicket)
    {
        try {
            // Relaciones anidadas para con la tabla tickets
            $showQualification = CalificacionTicket::with("ticket")
                ->with(["ticket.usuario"])
                ->with(["ticket.usuario.departamento"])
                ->with(["ticket.usuario.departamento.area"])
                ->with(["ticket.categoriaTicket"])
                ->with(["ticket.estadoTicket"])
                ->with(["ticket.prioridadTicket"])
                    ->findOrFail($calificacionTicket);

            return $showQualification;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Calificación de ticket no encontrada",
                404
            ));
        }
    }
}
