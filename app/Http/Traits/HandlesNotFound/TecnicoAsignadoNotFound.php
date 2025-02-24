<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\TecnicoAsignado;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait TecnicoAsignadoNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findTecnicoAsignadoOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $technicianId = $this->validateRequestId();

            // Se verifica si el técnico ingresado esta eliminado
            $isDeleted = TecnicoAsignado::where("id", $technicianId)
                ->whereNotNull("recurso_eliminado")
                ->exists();

            // Se muestra el mensaje del cacth
            if ($isDeleted) {
                throw new ModelNotFoundException();
            }

            // Relaciones anidadas para con la tabla usuarios
            $showTechnician = TecnicoAsignado::with("usuario")
                ->with(["usuario.departamento"])
                ->with(["usuario.departamento.area"])
            // Relaciones anidadas para con la tabla tickets
                ->with(["ticket.usuario"])
                ->with(["ticket.usuario.departamento"])
                ->with(["ticket.usuario.departamento.area"])
                ->with(["ticket.categoriaTicket"])
                ->with(["ticket.estadoTicket"])
                ->with(["ticket.prioridadTicket"])
                    ->whereNull("recurso_eliminado")
                    ->findOrFail($technicianId);

            return $showTechnician;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Técnico asignado no encontrado",
                404
            ));
        }
    }
}
