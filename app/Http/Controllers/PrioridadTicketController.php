<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\PrioridadTicket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PrioridadTicketController extends Controller
{
    public function index()
    {
        try {
            $allPriority = PrioridadTicket::all();

            if ($allPriority->isEmpty()) {
                return ApiResponse::success(
                    "Lista de prioridades de ticket vacia",
                    200,
                    $allPriority
                );
            }
            return ApiResponse::success(
                "Listado de prioridad de ticket",
                200,
                $allPriority
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                404
            );
        }
    }

    public function store(Request $request)
    {
        try {
            $newPriorityTicket = PrioridadTicket::create([
                "nombre_prioridad"=> $request->nombre_prioridad,
                "color_prioridad"=> $request->color_prioridad,
                "orden_prioridad"=> $request->orden_prioridad
            ]);

            return ApiResponse::success(
                "Prioridad de ticket creado con exito",
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
                "Prioridad ticket encontrado con exito",
                200,
                $showPriorityTicket
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Prioridad ticket no encontrado",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(Request $request, $prioridadTicket)
    {
        try {
            $updatePriorityTicket = PrioridadTicket::findOrFail($prioridadTicket);

            /* $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
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
            } */
            $updatePriorityTicket->update([
                "nombre_prioridad"=> $request->nombre_prioridad,
                "color_prioridad"=> $request->color_prioridad
            ]);

            return ApiResponse::success(
                "Prioridad ticket actualizado con exito",
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
                "Prioridad ticket eliminado con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Prioridad ticket no encontrado",
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
