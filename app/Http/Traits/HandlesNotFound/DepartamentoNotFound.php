<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait DepartamentoNotFound
{
    public function findDepartamentoOrFail($departamento)
    {
        try {
            $showDepartment = Departamento::with("area")
                ->findOrFail($departamento);

            return $showDepartment;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Departamento no encontrado",
                404
            ));
        }
    }
}
