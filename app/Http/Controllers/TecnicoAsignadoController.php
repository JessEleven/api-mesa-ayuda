<?php

namespace App\Http\Controllers;

use App\Http\Requests\TecnicoAsignado\StoreTecnicoAsignadoRequest;
use App\Http\Requests\TecnicoAsignado\UpdateTecnicoAsignadoRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\TecnicoAsignadoNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Models\EstadoTicket;
use App\Models\TecnicoAsignado;
use App\Models\Ticket;
use App\Services\TecnicoAsignadoModelHider;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

class TecnicoAsignadoController extends Controller
{
    // Reutilizamos los Traits
    use HandlesRequestId;
    use TecnicoAsignadoNotFound;

    public function index()
    {
        try {
            // Relaciones anidadas para con la tabla usuarios
            $allTechnicians = TecnicoAsignado::with("usuario")
                ->with(["usuario.departamento"])
                ->with(["usuario.departamento.area"])
            // Relaciones anidadas para con la tabla tickets
                ->with(["ticket.usuario"])
                ->with(["ticket.usuario.departamento"])
                ->with(["ticket.usuario.departamento.area"])
                ->with(["ticket.categoriaTicket"])
                ->with(["ticket.estadoTicket"])
                ->with(["ticket.prioridadTicket"])
                    ->whereNull("recurso_eliminado")
                    ->orderBy("id", "asc")
                    ->paginate("20");

            if ($allTechnicians->isEmpty()) {
                return ApiResponse::index(
                    "Lista de técnicos asignados vacía",
                    200,
                    $allTechnicians
                );
            }

            // Usando el servicio para ocultar los campos
            $allTechnicians->getCollection()->transform( function($technician) {
                return TecnicoAsignadoModelHider::hideTecnicoAsignadoFields($technician);
            });

            return ApiResponse::index(
                "Lista de técnicos asignados",
                200,
                $allTechnicians
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreTecnicoAsignadoRequest $request)
    {
        try {
            $newTechnician = TecnicoAsignado::create($request->validated());

            return ApiResponse::created(
                "Técnico asignado creado con éxito",
                201,
                $newTechnician->only([
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
            $showTechnician = $this->findTecnicoAsignadoOrFail($id);

            // Usando el servicio para ocultar los campos
            TecnicoAsignadoModelHider::hideTecnicoAsignadoFields($showTechnician);

            return ApiResponse::show(
                "Técnico asignado encontrado con éxito",
                200,
                $showTechnician
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

    public function update(UpdateTecnicoAsignadoRequest $request, $tecnicoAsignado)
    {
        try {
            $updateTechnician = TecnicoAsignado::findOrFail($tecnicoAsignado);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateTechnician->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar técnico asignado",
                    200,
                    array_intersect_key($newData, array_flip([
                        "created_at",
                        "updated_at"
                    ]))
                );
            }
            $updateTechnician->update($newData);

            return ApiResponse::updated(
                "Técnico asignado actualizado con éxito",
                200,
                $updateTechnician->refresh()->only([
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

    public function destroy()
    {
        try {
            // Uso de los Traits
            $id = $this->validateRequestId();

            // Se verifica si el técnico previamente ha sido eliminado
            $isDeleted = TecnicoAsignado::where("id", $id)
                ->whereNotNull("recurso_eliminado")
                ->exists();

            if ($isDeleted) {
                return ApiResponse::error(
                    "El técnico asignado ya no existe",
                    404
                );
            }
            $technicianId = $this->findTecnicoAsignadoOrFail($id);

            $ticketId = Ticket::find($technicianId->id_ticket);

            // Se obtiene el estado con el orden de prioridad más alto que existe
            $maxPriority = EstadoTicket::max("orden_prioridad");

            // Se busca el estado actual que tiene el ticket
            $currentStatus = EstadoTicket::find($ticketId->id_estado);

            // Si el técnico ha finalizado el ticket no se podra eliminar
            if ($currentStatus && $currentStatus->orden_prioridad === $maxPriority) {
                return ApiResponse::error(
                    "El técnico ha finalizado el ticket",
                    400,
                    [
                        "current_status"=> $currentStatus->nombre_estado,
                        "ticket_end_date"=> $ticketId->fecha_fin
                    ]
                );
            }
            $technicianId->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Técnico asignado eliminado con éxito",
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
