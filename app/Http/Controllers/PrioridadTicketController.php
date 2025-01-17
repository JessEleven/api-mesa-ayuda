<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrioridadTicket\StorePrioridadTicketRequest;
use App\Http\Requests\PrioridadTicket\UpdatePrioridadTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\PrioridadTicket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PrioridadTicketController extends Controller
{
    public function index()
    {
        try {
            $allPriorities = PrioridadTicket::orderBy("id", "asc")->paginate(20);

            if ($allPriorities->isEmpty()) {
                return ApiResponse::success(
                    "Lista de prioridades de ticket vacía",
                    200,
                    $allPriorities
                );
            }
            return ApiResponse::success(
                "Lista de prioridades de ticket",
                200,
                $allPriorities
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                404
            );
        }
    }

    public function store(StorePrioridadTicketRequest $request)
    {
        try {
            $newPriority = PrioridadTicket::create($request->validated());

            return ApiResponse::success(
                "Prioridad de ticket creada con éxito",
                201,
                $newPriority
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($prioridadTicket)
    {
        try {
            $showPriority = PrioridadTicket::findOrFail($prioridadTicket);

            return ApiResponse::success(
                "Prioridad de ticket encontrada con éxito",
                200,
                $showPriority
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Prioridad de ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdatePrioridadTicketRequest $request, $prioridadTicket)
    {
        try {
            $updatePriority = PrioridadTicket::findOrFail($prioridadTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updatePriority->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar prioridad de ticket",
                    200,
                    $newData
                );
            }
            $updatePriority->update($newData);

            return ApiResponse::success(
                "Prioridad de ticket actualizada con éxito",
                200,
                $updatePriority->refresh()
            );

        }   catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($prioridadTicket)
    {
        try {
            PrioridadTicket::findOrFail($prioridadTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Prioridad de ticket eliminada con éxito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Prioridad de ticket no encontrada",
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
