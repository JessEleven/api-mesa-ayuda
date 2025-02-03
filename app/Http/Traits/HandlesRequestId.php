<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesRequestId
{
    protected function getRequestId(): ? int
    {
        // Se obtiene el nombre del parámetro dinámico de la ruta
        $routeName = $this->route()?->parameterNames[0] ?? null;
        // Se obtiene el ID desde la ruta
        $id = $routeName ? $this->route($routeName) : null;

        // Se devuelve null si el ID no es un valor numérico
        if ($id !== null && !is_numeric($id)) {
            return null;
        }
        return $id;
    }

    protected function validateRequestId(): ? int
    {
        $id = $this->getRequestId();
        // Se valida que el ID sea numérico
        if ($id === null || !is_numeric($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }
        return (int) $id;
    }
}
