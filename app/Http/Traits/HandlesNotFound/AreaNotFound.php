<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\Area;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait AreaNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findAreaOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $areaId = $this->validateRequestId();

            $area = Area::findOrFail($areaId);
            return $area;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Área no encontrada",
                404
            ));
        }
    }
}
