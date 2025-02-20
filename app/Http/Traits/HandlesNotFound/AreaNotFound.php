<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\Area;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;


trait AreaNotFound
{
    public function findAreaOrFail($area)
    {
        try {
            return Area::findOrFail($area);

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Área no encontrada 🚀",
                404
            ));
        }
    }
}
