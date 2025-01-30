<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ValidatesPrioridadTicket
{
    // Se verifica si una prioridad de ticket está en uso
    protected function PriorityTicketInUse(int $id): void
    {
        $registeredTickets = Ticket::exists();

        if ($registeredTickets) {
            throw new HttpResponseException(ApiResponse::error(
                "Actualmente ya existen tickets registrados",
                422
            ));
        }
    }
}
