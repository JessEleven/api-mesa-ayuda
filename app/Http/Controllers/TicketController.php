<?php

namespace App\Http\Controllers;

use App\Helpers\CodigoTicketHelper;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesTicketStatus;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

class TicketController extends Controller
{
    // Reutilizando el trait
    use HandlesTicketStatus;

    public function index()
    {
        try {
            // Relación anidada entre las tablas usuarios y departamentos
            $allTickets = Ticket::with(["usuarios.departamentos"])
            // Relación anidada entre las tablas usuarios, departamentos y areas
                ->with(["usuarios.departamentos.areas"])
                ->with("categoria_tickets", "estado_tickets", "prioridad_tickets")
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

            // Para ocultar los FKs de la tabla
            $allTickets->getCollection()->transform(function ($ticket) {
                $ticket->makeHidden(["recurso_eliminado", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at"]);
                // Para ocultar los PKs, FKs algunos campos y timestamps de las tablas relaciones
                $ticket->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento", "created_at", "updated_at"]);
                $ticket->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
                $ticket->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
                $ticket->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
                $ticket->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                $ticket->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                return $ticket;
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

    public function show($ticket)
    {
        try {
            // Relación anidada entre las tablas usuarios y departamentos
            $showTicket = Ticket::with(["usuarios.departamentos"])
            // Relación anidada entre las tablas usuarios, departamentos y areas
                ->with(["usuarios.departamentos.areas"])
                ->with("categoria_tickets","estado_tickets", "prioridad_tickets")
                    ->whereNull("recurso_eliminado")
                    ->findOrFail($ticket);

            // Para ocultar los FKs de la tabla
            $showTicket->makeHidden(["recurso_eliminado", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at"]);
            // Para ocultar los PKs, FKs, algunos campos y timestamps de las tablas relaciones
            $showTicket->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento", "created_at", "updated_at"]);
            $showTicket->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
            $showTicket->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
            $showTicket->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
            $showTicket->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
            $showTicket->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);

            return ApiResponse::show(
                "Ticket encontrado con éxito",
                200,
                $showTicket
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Ticket no encontrado",
                404
            );

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
                        "descripcion"
                    ]))
                );
            }
            $updateTicket->update($newData);

            return ApiResponse::updated(
                "Ticket actualizado con éxito",
                200,
                $updateTicket->refresh()->only([
                    "descripcion",
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

    public function destroy($ticket)
    {
        try {
            // Se verifica si el ticket ya está finalizado antes de eliminarlo
            $this->ticketIsFinalized($ticket);

            $ticketId = Ticket::findOrFail($ticket);
            $ticketId->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Ticket eliminado con éxito",
                200,
                ["related"=> $baseRoute]
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
