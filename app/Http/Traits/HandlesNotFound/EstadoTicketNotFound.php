<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\EstadoTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait EstadoTicketNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findEstadoTicketOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $statusId = $this->validateRequestId();

            $showStatus = EstadoTicket::findOrFail($statusId);
            return $showStatus;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Estado de ticket no encontrado",
                404
            ));
        }
    }
}
