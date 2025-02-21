<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\PrioridadTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait PrioridadTicketNotFound
{
    public function findPrioridadTicketOrFail($prioridadTicket)
    {
        try {
            $priority = PrioridadTicket::findOrFail($prioridadTicket);
            return $priority;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Prioridad de ticket no encontrada",
                404
            ));
        }
    }
}
