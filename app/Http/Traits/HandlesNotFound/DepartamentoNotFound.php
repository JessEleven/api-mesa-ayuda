<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait DepartamentoNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findDepartamentoOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $departamentoId = $this->validateRequestId();

            $showDepartment = Departamento::with("area")
                ->findOrFail($departamentoId);

            return $showDepartment;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Departamento no encontrado",
                404
            ));
        }
    }
}
