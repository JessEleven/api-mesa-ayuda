<?php

namespace App\Http\Controllers;

use App\Http\Requests\Area\StoreAreaRequest;
use App\Http\Requests\Area\UpdateAreaRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Area;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class AreaController extends Controller
{
    public function index()
    {
        try {
            $allAreas = Area::orderBy("id", "asc")->paginate(20);

            if ($allAreas->isEmpty()) {
                return ApiResponse::success(
                    "Lista de áreas vacia",
                    200,
                    $allAreas
                );
            }
            return ApiResponse::success(
                "Listado de áreas",
                200,
                $allAreas
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreAreaRequest $request)
    {
        try {
            $newArea = Area::create($request->validated());

            return ApiResponse::success(
                "Area creada con exito",
                201,
                $newArea
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($area)
    {
        try {
            $showArea = Area::findOrFail($area);

            return ApiResponse::success(
                "Área encontrada con exito",
                200,
                $showArea
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Área no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateAreaRequest $request, $area)
    {
        try {
            $updateArea = Area::findOrFail($area);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateArea->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar área",
                    200,
                    $newData
                );
            }
            $updateArea->update($newData);

            return ApiResponse::success(
                "Área actualizada con exito",
                200,
                $updateArea->refresh()
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($area)
    {
        try {
            Area::findOrFail($area)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Área eliminada con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Área no encontrada",
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
