<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaTicket\StoreCategoriaTicketRequest;
use App\Http\Requests\CategoriaTicket\UpdateCategoriaTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\CategoriaTicketNotFound;
use App\Http\Traits\ValidatesRegisteredTickets;
use App\Models\CategoriaTicket;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoriaTicketController extends Controller
{
    // Reutilizando los Traits
    use ValidatesRegisteredTickets;
    use CategoriaTicketNotFound;

    public function index()
    {
        try {
            $allCategories = CategoriaTicket::orderBy("id", "asc")
                ->paginate(20);

            if ($allCategories->isEmpty()) {
                return ApiResponse::index(
                    "Lista de categorias de ticket vacía",
                    200,
                    $allCategories
                );
            }
            return ApiResponse::index(
                "Lista de categorias de ticket",
                200,
                $allCategories
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreCategoriaTicketRequest $request)
    {
        try {
            $newCategory = CategoriaTicket::create($request->validated());

            return ApiResponse::created(
                "Categoria de ticket creada con éxito",
                201,
                $newCategory->only([
                    "nombre_categoria",
                    "sigla_categoria",
                    "created_at"
                ])
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show()
    {
        try {
            // Uso del Trait
            $showCategory = $this->findCategoriaTicketOrFail();

            return ApiResponse::show(
                "Categoria de ticket encontrada con éxito",
                200,
                $showCategory
            );

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateCategoriaTicketRequest $request, $categoriaTicket)
    {
        try {
            $updateCategory = CategoriaTicket::findOrFail($categoriaTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateCategory->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar categoria de ticket",
                    200,
                    $newData
                );
            }
            $updateCategory->update($newData);

            return ApiResponse::updated(
                "Categoria de ticket actualizada con éxito",
                200,
                $updateCategory->refresh()->only([
                    "nombre_categoria",
                    "sigla_categoria",
                    "created_at",
                    "updated_at"
                ])
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy()
    {
        try {
            // Uso de los Traits
            $deleteCategory = $this->findCategoriaTicketOrFail();

            // Antes de eliminar la categoria se verifica si existen tickets
            $this->registeredOrActiveTickets();

            $deleteCategory->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Categoria de ticket eliminada con éxito",
                200,
                [
                    "related"=> $relativePath,
                    "api_version"=> $apiVersion
                ]
            );

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }
}
