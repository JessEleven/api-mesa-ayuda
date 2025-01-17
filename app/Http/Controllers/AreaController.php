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
                    "Lista de áreas vacía",
                    200,
                    $allAreas
                );
            }
            return ApiResponse::success(
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
            $validatedData = $request->validated();

            // Se obtiene primero la última secuencia global
            $lastSequence = Area::orderBy('id', 'desc')->value('secuencia_area');

            if ($lastSequence) {
                // Se extrae el número secuencial global
                $sequenceNumber = (int) substr($lastSequence, 0, 3);
                $sequenceNumber++;
            } else {
                // Si no existe, se inicializa en 1 la secuencia
                $sequenceNumber = 1;
            }

            // Se formatea la secuencia a 3 dígitos y se concatena con la sigla
            $secuencia = sprintf('%03d-%s', $sequenceNumber, $validatedData['sigla_area']);

            // Se agrega la secuencia generada a los datos validados
            $validatedData['secuencia_area'] = $secuencia;

            $newArea = Area::create($validatedData);

            return ApiResponse::success(
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

            return ApiResponse::success(
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
                return ApiResponse::success(
                    "No hay cambios para actualizar el área",
                    200,
                    $newData
                );
            }

            // Se verifica si la sigla ha cambiado
            if (isset($newData['sigla_area']) && $newData['sigla_area'] != $updateArea->sigla_area) {

                // Si ha cambiado, se conserva la secuencia y se actualiza solo la sigla
                $currentSequence = $updateArea->secuencia_area;

                // Se extrae la parte numérica de la secuencia actual
                $sequenceNumber = substr($currentSequence, 0, 3);

                // Se crea la nueva secuencia concatenando el número de la secuencia con la nueva sigla
                $newData['secuencia_area'] = sprintf('%03d-%s', $sequenceNumber, $newData['sigla_area']);
            }
            $updateArea->update($newData);

            return ApiResponse::success(
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

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Área eliminada con éxito",
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
