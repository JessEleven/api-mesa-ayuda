<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait TicketNotFound
{
    public function findTicketOrFail($ticket)
    {
        try {
            // Se verifica si el ticket ingresado esta eliminado
            $isDeleted = Ticket::where("id", $ticket)
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
                    ->findOrFail($ticket);

            return $showTicket;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Ticket no encontrado",
                404
            ));
        }
    }
}
