<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\TecnicoAsignado;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait TecnicoAsignadoNotFound
{
    public function findTecnicoAsignadoOrFail($tecnicoAsignado)
    {
        try {
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
                    ->findOrFail($tecnicoAsignado);

            return $showTechnician;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Técnico asignado no encontrado",
                404
            ));
        }
    }
}
