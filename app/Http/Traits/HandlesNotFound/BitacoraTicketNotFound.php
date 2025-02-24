<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\BitacoraTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait BitacoraTicketNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findBitacoraTicketOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $logId = $this->validateRequestId();

            // Se verifica si la bitácora ingresada esta eliminada
            $isDeleted = BitacoraTicket::where("id", $logId)
                ->whereNotNull("recurso_eliminado")
                ->exists();

            // Se muestra el mensaje del cacth
            if ($isDeleted) {
                throw new ModelNotFoundException();
            }

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
                    ->findOrFail($logId);

            return $showLog;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Bitácora de ticket no encontrada",
                404
            ));
        }
    }
}
