<?php

namespace App\Http\Controllers;

use App\Helpers\CodigoTicketHelper;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\TicketNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Http\Traits\HandlesTicketStatus;
use App\Models\EstadoTicket;
use App\Models\TecnicoAsignado;
use App\Models\Ticket;
use App\Services\TicketModelHider;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

class TicketController extends Controller
{
    // Reutilizando el trait
    use HandlesTicketStatus;
    use HandlesRequestId;
    use TicketNotFound;

    public function index()
    {
        try {
            // Relación anidada entre las tablas usuarios y departamentos
            $allTickets = Ticket::with(["usuario.departamento"])
            // Relación anidada entre las tablas usuarios, departamentos y areas
                ->with(["usuario.departamento.area"])
                ->with("categoriaTicket", "estadoTicket", "prioridadTicket")
                    ->whereNull("recurso_eliminado")
                    ->orderBy("id", "asc")
                    ->paginate(20);

            if ($allTickets->isEmpty()) {
                return ApiResponse::index(
                    "Lista de tickets vacía",
                    200,
                    $allTickets
                );
            }

            // Usando el servicio para ocultar los campos
            $allTickets->getCollection()->transform(function ($ticket) {
                return TicketModelHider::hideTicketFields($ticket);
            });

            return ApiResponse::index(
                "Lista de tickets",
                200,
                $allTickets
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreTicketRequest $request)
    {
        try {
            // Buscar el estado inicial para el ticket
            $defaultState = EstadoTicket::where("orden_prioridad", 1)->first();

            if (!$defaultState) {
                return ApiResponse::error(
                    "Estado inicial para el ticket no encontrado",
                    500
                );
            }

            // Uso del helper (app/Helpers/CodigoTicketHelper.php) para crear codido_ticket
            $codigoTicket = CodigoTicketHelper::generateCodigoTicket(
                $request->id_usuario, $request->id_categoria
            );

            $newTicket = Ticket::create(array_merge(
                $request->validated(), [
                    "codigo_ticket"=> $codigoTicket,
                    "fecha_inicio"=> now(),
                    "id_estado"=> $defaultState->id
                ]
            ));

            return ApiResponse::created(
                "Ticket creado con éxito",
                201,
                $newTicket->only([
                    "codigo_ticket",
                    "descripcion",
                    "fecha_inicio"
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
            $showTicket = $this->findTicketOrFail($id);

            // Usando el servicio para ocultar los campos
            TicketModelHider::hideTicketFields($showTicket);

            return ApiResponse::show(
                "Ticket encontrado con éxito",
                200,
                $showTicket
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

    public function update(UpdateTicketRequest $request, $ticket)
    {
        try {
            $updateTicket = Ticket::findOrFail($ticket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateTicket->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar ticket",
                    200,
                    array_intersect_key($newData, array_flip([
                        "asunto",
                        "descripcion"
                    ]))
                );
            }
            $updateTicket->update($newData);

            return ApiResponse::updated(
                "Ticket actualizado con éxito",
                200,
                $updateTicket->refresh()->only([
                    "asunto",
                    "descripcion",
                    "fecha_inicio",
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

            // Se verifica si el ticket ya está finalizado antes de eliminarlo
            $this->ticketIsFinalized($id);

            $ticketId = $this->findTicketOrFail($id);

            // Si el ticket está asignado, no se puede eliminar
            $isAssigned = TecnicoAsignado::where("id_ticket", $id)
                ->exists();

            if ($isAssigned) {
                return ApiResponse::error(
                    "El ticket ha sido asignado",
                    400,
                    ["ticket_created"=> $ticketId->created_at]
                );
            }
            $ticketId->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Ticket eliminado con éxito",
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
