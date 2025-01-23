<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends Controller
{
    public function index()
    {
        try {
            // Relación anidada entre las tablas usuarios y departamentos
            $allTickets = Ticket::with(["usuarios.departamentos"])
            // Relación anidada entre las tablas usuarios, departamentos y areas
                ->with(["usuarios.departamentos.areas"])
                ->with("categoria_tickets", "estado_tickets", "prioridad_tickets")
                    ->orderBy("id", "asc")
                    ->paginate(20);

            if ($allTickets->isEmpty()) {
                return ApiResponse::success(
                    "Lista de tickets vacía",
                    200,
                    $allTickets
                );
            }

            // Para ocultar los FKs de la tabla
            $allTickets->getCollection()->transform(function ($ticket) {
                $ticket->makeHidden(["id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at"]);
                // Para ocultar los PKs, FKs algunos campos y timestamps de las tablas relaciones
                $ticket->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento", "created_at", "updated_at"]);
                $ticket->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
                $ticket->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
                $ticket->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
                $ticket->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                $ticket->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                return $ticket;
            });

            return ApiResponse::success(
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

            $newTicket = Ticket::create(array_merge(
                $request->validated(), [
                    "fecha_inicio"=> now(),
                    "id_estado"=> $defaultState->id
                ]
            ));

            return ApiResponse::success(
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
                    ->findOrFail($ticket);

            // Para ocultar los FKs de la tabla
            $showTicket->makeHidden(["id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at"]);
            // Para ocultar los PKs, FKs, algunos campos y timestamps de las tablas relaciones
            $showTicket->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento", "created_at", "updated_at"]);
            $showTicket->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
            $showTicket->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
            $showTicket->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
            $showTicket->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
            $showTicket->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);

            return ApiResponse::success(
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
                return ApiResponse::success(
                    "No hay cambios para actualizar ticket",
                    200,
                    array_intersect_key($newData, array_flip([
                        "descripcion"
                    ]))
                );
            }
            $updateTicket->update($newData);

            return ApiResponse::success(
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
            Ticket::findOrFail($ticket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Ticket eliminado con éxito",
                200,
                ["related"=> $baseRoute]
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
}
