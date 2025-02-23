<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\TicketNotFound;
use App\Models\EstadoTicket;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesTicketStatus
{
    // Reutilizando el Trait
    use TicketNotFound;

    public function ticketIsFinalized()
    {
        // El Trait ya trae si el ticket ingresado existe o esta eliminado
        // si se cumple muestra el catch (mensaje y 404)
        $ticketId = $this->findTicketOrFail();

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
                [
                    "current_status"=> $currentStatus->nombre_estado,
                    "ticket_finished_at"=> $ticketId->fecha_fin
                ]
            ));
        }
        return $ticketId;
    }
}
