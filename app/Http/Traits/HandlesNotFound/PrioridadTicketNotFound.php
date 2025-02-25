<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\PrioridadTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait PrioridadTicketNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findPrioridadTicketOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $priorityId = $this->validateRequestId();

            $showPriority = PrioridadTicket::findOrFail($priorityId);
            return $showPriority;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Prioridad de ticket no encontrada",
                404
            ));
        }
    }
}
