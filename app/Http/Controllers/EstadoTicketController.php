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
            $allStatus = EstadoTicket::orderBy("id", "asc")->paginate(20);

            if ($allStatus->isEmpty()) {
                return ApiResponse::success(
                    "Lista de estados de ticket vacia",
                    200,
                    $allStatus
                );
            }
            return ApiResponse::success(
                "Listado de estados de ticket",
                200,
                $allStatus
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
            $newStatusTicket = EstadoTicket::create($request->validated());

            return ApiResponse::success(
                "Estado de ticket creado con exito",
                201,
                $newStatusTicket
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
            $showStatusTicket = EstadoTicket::findOrFail($estadoTicket);

            return ApiResponse::success(
                "Estado ticket encontrado con exito",
                200,
                $showStatusTicket
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Estado ticket no encontrado",
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
            $updateStatusTicket = EstadoTicket::findOrFail($estadoTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateStatusTicket->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar estado ticket",
                    200,
                    $newData
                );
            }
            $updateStatusTicket->update($newData);

            return ApiResponse::success(
                "Estado ticket actualizado con exito",
                200,
                $updateStatusTicket->refresh()
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
                "Estado ticket eliminado con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Estado ticket no encontrado",
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
