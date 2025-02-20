<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\BitacoraTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait BitacoraTicketNotFound
{
    public function findBitacoraTicketOrFail($bitacoraTicket)
    {
        try {
            // Relaciones anidadas para con la tabla tecnico_asignados
            $showLog = BitacoraTicket::with(["tecnicoAsignado.usuario.departamento"])
                ->with(["tecnicoAsignado.usuario.departamento.area"])
            // Relaciones anidadas para con la tabla tickets
                ->with(["tecnicoAsignado.ticket.usuario"])
                ->with(["tecnicoAsignado.ticket.usuario.departamento"])
                ->with(["tecnicoAsignado.ticket.usuario.departamento.area"])
                ->with(["tecnicoAsignado.ticket.categoriaTicket"])
                ->with(["tecnicoAsignado.ticket.estadoTicket"])
                ->with(["tecnicoAsignado.ticket.prioridadTicket"])
                    ->whereNull("recurso_eliminado")
                    ->findOrFail($bitacoraTicket);

            return $showLog;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Bitácora de ticket no encontrada",
                404
            ));
        }
    }
}
