<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\CalificacionTicket;
use App\Models\EstadoTicket;
use App\Models\Ticket;
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

    public function ticketQualifiedAt($operation)
    {
        $qualifierId = $this->findCalificacionTicketOrFail();

        // Se busca el ticket asociado a la calificación (id_ticket)
        $ticketId = Ticket::where("id", $qualifierId->id_ticket)->first();

        if (!$ticketId) {
            throw new HttpResponseException(ApiResponse::error(
                "Ticket no encontrado",
                404
            ));
        }

        // Se busca el estado actual que tiene el ticket
        $currentStatus = EstadoTicket::where("id", $ticketId->id_estado)->first();

        // Dependiendo del tipo de operación update() o destroy() se muesta su mensaje
        $operationMessage = $operation === "update"
            ? "No se puede actualizar la calificación"
            : "El ticket ya está calificado";

        throw new HttpResponseException(ApiResponse::error(
            $operationMessage,
            400,
            [
                "current_status"=> $currentStatus->nombre_estado,
                "ticket_finished_at"=> $ticketId->fecha_fin,
                "ticket_qualified_at"=> $qualifierId->created_at
            ]
        ));
    }
}
