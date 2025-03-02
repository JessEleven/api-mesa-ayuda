<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrioridadTicket\StorePrioridadTicketRequest;
use App\Http\Requests\PrioridadTicket\UpdatePrioridadTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\PrioridadTicketNotFound;
use App\Http\Traits\HandlesServerErrors\HandleException;
use App\Http\Traits\ValidatesRegisteredTickets;
use App\Models\PrioridadTicket;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class PrioridadTicketController extends Controller
{
    // Reutilizando los Traits
    use ValidatesRegisteredTickets;
    use PrioridadTicketNotFound;
    use HandleException;

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

        // Uso del Trait en index, store, show, update y destroy
        } catch (Exception $e) {
            return $this->isAnException($e);
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
            return $this->isAnException($e);
        }
    }

    public function show()
    {
        try {
            // Uso del Trait
            $showPriority = $this->findPrioridadTicketOrFail();

            return ApiResponse::show(
                "Prioridad de ticket encontrada con éxito",
                200,
                $showPriority
            );

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return $this->isAnException($e);
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

        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }

    public function destroy()
    {
        try {
            // Uso de los Traits
            $deletePriority = $this->findPrioridadTicketOrFail();

            // Antes de eliminar la prioridad se verifica si existen tickets
            $this->registeredOrActiveTickets();

            $deletePriority->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Prioridad de ticket eliminada con éxito",
                200,
                [
                    "relative_path"=> $relativePath,
                    "version"=> $apiVersion
                ]
            );

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return $this->isAnException($e);;
        }
    }
}
