<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstadoTicket\StoreEstadoTicketRequest;
use App\Http\Requests\EstadoTicket\UpdateEstadoTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\EstadoTicketNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Http\Traits\ValidatesEstadoTicket;
use App\Models\EstadoTicket;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class EstadoTicketController extends Controller
{
    // Reutilizando los Traits
    use ValidatesEstadoTicket;
    use HandlesRequestId;
    use EstadoTicketNotFound;

    public function index()
    {
        try {
            $allStatuses = EstadoTicket::orderBy("id", "asc")
                ->paginate(20);

            if ($allStatuses->isEmpty()) {
                return ApiResponse::index(
                    "Lista de estados de ticket vacía",
                    200,
                    $allStatuses
                );
            }
            return ApiResponse::index(
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

            return ApiResponse::created(
                "Estado de ticket creado con éxito",
                201,
                $newStatus->only([
                    "nombre_estado",
                    "color_estado",
                    "orden_prioridad",
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

    public function show()
    {
        try {
            // Uso de los Traits
            $id = $this->validateRequestId();
            $showStatus = $this->findEstadoTicketOrFail($id);

            return ApiResponse::show(
                "Estado de ticket encontrado con éxito",
                200,
                $showStatus
            );

        } catch (HttpResponseException $e) {
            return $e->getResponse();

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
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar estado de ticket",
                    200,
                    $newData
                );
            }
            $updateStatus->update($newData);

            return ApiResponse::updated(
                "Estado de ticket actualizado con éxito",
                200,
                $updateStatus->refresh()->only([
                    "nombre_estado",
                    "color_estado",
                    "created_at",
                    "updated_at"
                ])
            );

        }   catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy()
    {
        try {
            // Uso de los Traits
            $id = $this->validateRequestId();
            $deleteStatus = $this->findEstadoTicketOrFail($id);

            // Se valida si está en uso el estado de ticket antes de eliminar
            $this->EstadoTicketInUse($id);

            $deleteStatus->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Estado de ticket eliminado con éxito",
                200,
                [
                    "related"=> $relativePath,
                    "api_version"=> $apiVersion
                ]
            );

        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }
}
