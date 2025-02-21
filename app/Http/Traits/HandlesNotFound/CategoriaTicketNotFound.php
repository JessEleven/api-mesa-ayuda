<?php

namespace App\Http\Traits\HandlesNotFound;

use App\Http\Responses\ApiResponse;
use App\Models\CategoriaTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CategoriaTicketNotFound
{
    public function findCategoriaTicketOrFail($categoriaTicket)
    {
        try {
            $category = CategoriaTicket::findOrFail($categoriaTicket);
            return $category;

        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(ApiResponse::error(
                "Categoria de ticket no encontrada",
                404
            ));
        }
    }
}
