<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesTicketStatus
{
    public function ticketIsFinalized($ticket)
    {
        // Se verifica si el ticket ingresado existe o esta eliminado
        $ticketId = Ticket::find($ticket);
        $ticketDeleted = Ticket::where("id", $ticket)
            ->whereNotNull("recurso_eliminado")
            ->exists();

        if (!$ticketId || $ticketDeleted) {
            throw new HttpResponseException(ApiResponse::error(
                "Ticket no encontrado",
                404
            ));
        }

        // Se obtiene el estado con el orden de prioridad más alto que existe
        $maxPriority = EstadoTicket::max("orden_prioridad");

        // Se busca el estado actual que tiene el ticket
        $currentStatus = EstadoTicket::find($ticketId->id_estado);

        // Si el ticket está en el estado con la mayor orden de prioridad no se podra
        // editar (UpdateTicketRequest.php) o eliminar (TicketController.php)
        if ($currentStatus && $currentStatus->orden_prioridad === $maxPriority) {
            throw new HttpResponseException(ApiResponse::error(
                "El ticket ha sido finalizado",
                400,
                ["current_status"=> $currentStatus->nombre_estado]
            ));
        }
        return $ticketId;
    }
}
