<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait UsuarioNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findUsuarioOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $userId = $this->validateRequestId();

            // Relación anidada entre las tablas departamentos y areas
            // Se ignoran los usuarios designados a técnicos
            $showUser = Usuario::with(["departamento.area"])
                ->whereDoesntHave("tecnicoAsignado")
                ->findOrFail($userId);

            return $showUser;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Usuario no encontrado",
                404
            ));
        }
    }
}
