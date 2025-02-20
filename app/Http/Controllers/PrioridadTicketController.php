<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrioridadTicket\StorePrioridadTicketRequest;
use App\Http\Requests\PrioridadTicket\UpdatePrioridadTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\PrioridadTicketNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Http\Traits\ValidatesPrioridadTicket;
use App\Models\PrioridadTicket;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class PrioridadTicketController extends Controller
{
    // Reutilizamos los Traits
    use ValidatesPrioridadTicket;
    use HandlesRequestId;
    use PrioridadTicketNotFound;

    public function index()
    {
        try {
            $allPriorities = PrioridadTicket::orderBy("id", "asc")
                ->paginate(20);

            if ($allPriorities->isEmpty()) {
                return ApiResponse::index(
                    "Lista de prioridades de ticket vacía",
                    200,
                    $allPriorities
                );
            }
            return ApiResponse::index(
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

            return ApiResponse::created(
                "Prioridad de ticket creada con éxito",
                201,
                $newPriority->only([
                    "nombre_prioridad",
                    "color_prioridad",
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
            $showPriority = $this->findPrioridadTicketOrFail($id);

            return ApiResponse::show(
                "Prioridad de ticket encontrada con éxito",
                200,
                $showPriority
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
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar prioridad de ticket",
                    200,
                    $newData
                );
            }
            $updatePriority->update($newData);

            return ApiResponse::updated(
                "Prioridad de ticket actualizada con éxito",
                200,
                $updatePriority->refresh()->only([
                    "nombre_prioridad",
                    "color_prioridad",
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
            $deletePriority = $this->findPrioridadTicketOrFail($id);

            // Se valida si está en uso la prioridad de ticket antes de eliminar
            $this->PriorityTicketInUse($id);

            $deletePriority->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Prioridad de ticket eliminada con éxito",
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
