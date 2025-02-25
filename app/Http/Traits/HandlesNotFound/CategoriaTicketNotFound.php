<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\CategoriaTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CategoriaTicketNotFound
{
    // Reutilizando el Trait
    use HandlesRequestId;

    public function findCategoriaTicketOrFail()
    {
        try {
            // Se obtiene y valida el ID desde la request a travez del Trait HandlesRequestId
            $categoryId = $this->validateRequestId();

            $showCategory = CategoriaTicket::findOrFail($categoryId);
            return $showCategory;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Categoria de ticket no encontrada",
                404
            ));
        }
    }
}
