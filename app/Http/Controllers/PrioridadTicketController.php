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
                    "Lista de prioridades de ticket vacia",
                    200,
                    $allPriorities
                );
            }
            return ApiResponse::success(
                "Listado de prioridad de ticket",
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
            $newPriorityTicket = PrioridadTicket::create($request->validated());

            return ApiResponse::success(
                "Prioridad de ticket creada con exito",
                201,
                $newPriorityTicket
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
            $showPriorityTicket = PrioridadTicket::findOrFail($prioridadTicket);

            return ApiResponse::success(
                "Prioridad ticket encontrada con exito",
                200,
                $showPriorityTicket
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Prioridad ticket no encontrada",
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
            $updatePriorityTicket = PrioridadTicket::findOrFail($prioridadTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updatePriorityTicket->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar prioridad ticket",
                    200,
                    $newData
                );
            }
            $updatePriorityTicket->update($newData);

            return ApiResponse::success(
                "Prioridad ticket actualizada con exito",
                200,
                $updatePriorityTicket->refresh()
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
                "Prioridad ticket eliminada con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Prioridad ticket no encontrada",
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
