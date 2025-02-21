<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\EstadoTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait EstadoTicketNotFound
{
    public function findEstadoTicketOrFail($estadoTicket)
    {
        try {
            $status = EstadoTicket::findOrFail($estadoTicket);
            return $status;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Estado de ticket no encontrado",
                404
            ));
        }
    }
}
