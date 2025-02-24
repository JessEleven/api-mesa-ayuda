<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\CalificacionTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CalificacionTicketNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findCalificacionTicketOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $qualificationId = $this->validateRequestId();

            // Relaciones anidadas para con la tabla tickets
            $showQualification = CalificacionTicket::with("ticket")
                ->with(["ticket.usuario"])
                ->with(["ticket.usuario.departamento"])
                ->with(["ticket.usuario.departamento.area"])
                ->with(["ticket.categoriaTicket"])
                ->with(["ticket.estadoTicket"])
                ->with(["ticket.prioridadTicket"])
                    ->findOrFail($qualificationId);

            return $showQualification;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Calificación de ticket no encontrada",
                404
            ));
        }
    }
}
