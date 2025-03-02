<?php

namespace App\Http\Traits\HandlesServerErrors;

use App\Http\Responses\ApiResponse;
use Exception;

trait HandleException
{
    protected function isAnException(Exception $e)
    {
        // Para simular un error y verificar si el Trait se ejecutaria
        // estas opciones deberían llamar al catch de cada controlador

        // Al inicio del método dentro del try
        // throw new Exception("Prueba de error");

        // Hacer una consulta de un método no existente, ej(s).
        // $allTickets->metodoInexistente();
        // $showArea->metodoInexistente();

        return ApiResponse::error(
            "Ha ocurrido un error inesperado",
            500
        );
    }
}
