<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ValidatesRegisteredTickets
{
    // Para crear o eliminar una o varias categorias de tickets, estados de tickets y
    // prioridades de tickets por el admin, primero no deben existir tickets regristrados,
    // es decir la tabla tickets tiene que estar vacía
    protected function registeredOrActiveTickets()
    {
        $allTickets = Ticket::count();
        $ticketsInUse = Ticket::whereNull("recurso_eliminado")->count();

        if ($allTickets || $ticketsInUse > 0) {
            // Si no hay tickets activos es porque han sido eliminados (recurso_eliminado)
            // y se muestra un valor por defecto
            $op = $ticketsInUse ?: "Registros pendientes";

            throw new HttpResponseException(ApiResponse::error(
                "Ya existen tickets registrados",
                400,
                [
                    "total_tickets_registered"=> $allTickets,
                    "total_active_tickets"=> $op
                ]
            ));
        }
    }
}
