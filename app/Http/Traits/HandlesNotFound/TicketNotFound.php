<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait TicketNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findTicketOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $ticketId = $this->validateRequestId();

            // Se verifica si el ticket ingresado esta eliminado
            $isDeleted = Ticket::where("id", $ticketId)
                ->whereNotNull("recurso_eliminado")
                ->exists();

            // Se muestra el mensaje del cacth
            if ($isDeleted) {
                throw new ModelNotFoundException();
            }

            // Relación anidada entre las tablas usuarios y departamentos
            $showTicket = Ticket::with(["usuario.departamento"])
            // Relación anidada entre las tablas usuarios, departamentos y areas
                ->with(["usuario.departamento.area"])
                ->with("categoriaTicket","estadoTicket", "prioridadTicket")
                    ->whereNull("recurso_eliminado")
                    ->findOrFail($ticketId);

            return $showTicket;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Ticket no encontrado",
                404
            ));
        }
    }
}
