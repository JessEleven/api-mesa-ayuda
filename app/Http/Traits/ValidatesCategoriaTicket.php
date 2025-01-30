<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ValidatesCategoriaTicket
{
    // Se verifica si una categoría de ticket está en uso
    protected function CategoryTicketInUse(int $id): void
    {
        $registeredTickets = Ticket::exists();

        if ($registeredTickets) {
            throw new HttpResponseException(ApiResponse::error(
                "Actualmente existen ya tickets registrados",
                422
            ));
        }
    }
}
