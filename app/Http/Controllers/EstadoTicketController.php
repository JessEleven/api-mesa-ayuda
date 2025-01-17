<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstadoTicket\StoreEstadoTicketRequest;
use App\Http\Requests\EstadoTicket\UpdateEstadoTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\EstadoTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EstadoTicketController extends Controller
{
    public function index()
    {
        try {
            $allStatuses = EstadoTicket::orderBy("id", "asc")->paginate(20);

            if ($allStatuses->isEmpty()) {
                return ApiResponse::success(
                    "Lista de estados de ticket vacía",
                    200,
                    $allStatuses
                );
            }
            return ApiResponse::success(
                "Lista de estados de ticket",
                200,
                $allStatuses
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreEstadoTicketRequest $request)
    {
        try {
            $newStatus = EstadoTicket::create($request->validated());

            return ApiResponse::success(
                "Estado de ticket creado con éxito",
                201,
                $newStatus
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($estadoTicket)
    {
        try {
            $showStatus = EstadoTicket::findOrFail($estadoTicket);

            return ApiResponse::success(
                "Estado de ticket encontrado con éxito",
                200,
                $showStatus
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Estado de ticket no encontrado",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateEstadoTicketRequest $request, $estadoTicket)
    {
        try {
            $updateStatus = EstadoTicket::findOrFail($estadoTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateStatus->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar el estado de ticket",
                    200,
                    $newData
                );
            }
            $updateStatus->update($newData);

            return ApiResponse::success(
                "Estado de ticket actualizado con éxito",
                200,
                $updateStatus->refresh()
            );

        }   catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($estadoTicket)
    {
        try {
            EstadoTicket::findOrFail($estadoTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Estado de ticket eliminado con éxito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Estado de ticket no encontrado",
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
