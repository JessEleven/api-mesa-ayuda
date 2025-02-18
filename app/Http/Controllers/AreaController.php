<?php

namespace App\Http\Controllers;

use App\Helpers\SecuenciaAreaHelper;
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
                return ApiResponse::index(
                    "Lista de áreas vacía",
                    200,
                    $allAreas
                );
            }
            return ApiResponse::index(
                "Lista de áreas",
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
            // Se obtiene la secuencia generada usando el helper
            $secuenciaArea = SecuenciaAreaHelper::generateSequenceArea(
                $request->sigla_area
            );

            // Se agrega la secuencia generada a los datos validados
            $newArea = Area::create(array_merge(
                $request->validated(), [
                    "secuencia_area"=> $secuenciaArea
                ]
            ));

            return ApiResponse::created(
                "Área creada con éxito",
                201,
                $newArea->only([
                    "nombre_area",
                    "sigla_area",
                    "secuencia_area",
                    "peso_prioridad",
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

    public function show($area)
    {
        try {
            $showArea = Area::findOrFail($area);

            return ApiResponse::show(
                "Área encontrada con éxito",
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
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar el área",
                    200,
                    $newData
                );
            }

            // Se verifica si la sigla ha cambiado
            if (isset($newData["sigla_area"]) && $newData["sigla_area"] != $updateArea->sigla_area) {
                // Si ha cambiado, se actualiza la secuencia usando el helper
                $newData["secuencia_area"] = SecuenciaAreaHelper::updateSequenceArea(
                    $updateArea, $newData["sigla_area"]
                );
            }
            $updateArea->update($newData);

            return ApiResponse::updated(
                "Área actualizada con éxito",
                200,
                $updateArea->refresh()->only([
                    "nombre_area",
                    "sigla_area",
                    "secuencia_area",
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

    public function destroy($area)
    {
        try {
            Area::findOrFail($area)->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Área eliminada con éxito",
                200,
                [
                    "related"=> $relativePath,
                    "api_version"=> $apiVersion
                ]
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
