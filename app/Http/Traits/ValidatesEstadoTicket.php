<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ValidatesEstadoTicket
{
    // Se verifica si un estado de ticket está en uso
    protected function EstadoTicketInUse(int $id): void
    {
        $registeredTickets = Ticket::count();

        if ($registeredTickets > 0) {
            throw new HttpResponseException(ApiResponse::error(
                "Ya existen tickets registrados",
                422,
                ["total_tickets"=> $registeredTickets]
            ));
        }
    }
}
