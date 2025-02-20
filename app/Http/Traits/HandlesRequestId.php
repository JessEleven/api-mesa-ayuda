<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

trait HandlesRequestId
{
    protected function getRequestId(): ? int
    {
        // Se obtiene la request actual
        $request = app(Request::class);

        // Se obtiene el nombre del parámetro dinámico de la ruta
        $routeName = $request->route()?->parameterNames[0] ?? null;

        // Se obtiene el ID desde la ruta
        $id = $routeName ? $request->route($routeName) : null;

        // Se devuelve null si el ID no es un valor numérico
        $isNumeric = is_numeric($id) ? (int) $id : null;

        return $isNumeric;
    }

    protected function validateRequestId(): int
    {
        $id = $this->getRequestId();

        // Se valida que el ID sea numérico
        if ($id === null) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }
        return (int) $id;
    }
}
