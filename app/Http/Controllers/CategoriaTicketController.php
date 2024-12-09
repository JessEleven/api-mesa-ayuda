<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaTicket\StoreCategoriaTicketRequest;
use App\Http\Requests\CategoriaTicket\UpdateCategoriaTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\CategoriaTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class CategoriaTicketController extends Controller
{
    public function index()
    {
        try {
            $allCategories = CategoriaTicket::orderBy("id", "asc")->paginate(20);

            if ($allCategories->isEmpty()) {
                return ApiResponse::success(
                    "Lista de categorias de ticket vacia",
                    200,
                    $allCategories
                );
            }
            return ApiResponse::success(
                "Listado de categorias de ticket",
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

            return ApiResponse::success(
                "Categoria de ticket creada con exito",
                201,
                $newCategory
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($categoriaTicket)
    {
        try {
            $showCategory = CategoriaTicket::findOrFail($categoriaTicket);

            return ApiResponse::success(
                "Categoria ticket encontrada con exito",
                200,
                $showCategory
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Categoria ticket no encontrada",
                404
            );

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
                return ApiResponse::success(
                    "No hay cambios para actualizar categoria ticket",
                    200,
                    $newData
                );
            }
            $updateCategory->update($newData);

            return ApiResponse::success(
                "Categoria ticket actualizada con exito",
                200,
                $updateCategory->refresh()
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($categoriaTicket)
    {
        try {
            CategoriaTicket::findOrFail($categoriaTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Categoria ticket eliminada con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Categoria ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }
}
