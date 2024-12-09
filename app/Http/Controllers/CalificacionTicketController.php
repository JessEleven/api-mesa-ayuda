<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalificacionTicket\StoreCalificacionTicketRequest;
use App\Http\Requests\CalificacionTicket\UpdateCalificacionTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\CalificacionTicket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CalificacionTicketController extends Controller
{
    public function index()
    {
        try {
            $allQualification = CalificacionTicket::orderBy("id", "asc")->paginate(20);

            if ($allQualification->isEmpty()) {
                return ApiResponse::success(
                    "Lista de calificaciones de ticket vacia",
                    200,
                    $allQualification
                );
            }
            return ApiResponse::success(
                "Listado de calificaciones de ticket",
                200,
                $allQualification
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreCalificacionTicketRequest $request)
    {
        try {
            $newQualification = CalificacionTicket::create($request->validated());

            return ApiResponse::success(
                "Calificación de ticket creada con exito",
                201,
                $newQualification
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($calificacionTicket)
    {
        try {
            $showQualification = CalificacionTicket::findOrFail($calificacionTicket);

            return ApiResponse::success(
                "Calificación ticket encontrada con exito",
                200,
                $showQualification
            );

        } catch(ModelNotFoundException $e) {
            return ApiResponse::error(
                "Calificación ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateCalificacionTicketRequest $request, $calificacionTicket)
    {
        try {
            $updateQualification = CalificacionTicket::findOrFail($calificacionTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateQualification->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar calificación ticket",
                    200,
                    $newData
                );
            }
            $updateQualification->update($newData);

            return ApiResponse::success(
                "Calificación ticket actualizada con exito",
                200,
                $updateQualification->refresh()
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($calificacionTicket)
    {
        try {
            CalificacionTicket::findOrFail($calificacionTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Calificación ticket eliminada con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Calificación ticket no encontrada",
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
