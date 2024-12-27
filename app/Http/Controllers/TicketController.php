<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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

            // Para ocultar el FKs de la tabla
            $allTickets->getCollection()->transform(function ($ticket) {
                $ticket->makeHidden(["id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at"]);
                // Para ocultar los PKs, FKs y timestamps de las tablas relaciones
                $ticket->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
                $ticket->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
                $ticket->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
                $ticket->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
                $ticket->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                $ticket->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                return $ticket;
            });

            if ($allTickets->isEmpty()) {
                return ApiResponse::success(
                    "Lista de tickets vacia",
                    200,
                    $allTickets
                );
            }
            return ApiResponse::success(
                "Listado de tickets",
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

    public function store(Request $request)
    {
        try {
            $newTicket = Ticket::create([
                "codigo_ticket"=> $request->codigo_ticket,
                "descripcion"=> $request->descripcion,
                "fecha_inicio"=> $request->fecha_inicio,
                "fecha_fin"=> $request->fecha_fin,
                "id_categoria"=> $request->id_categoria,
                "id_usuario"=> $request->id_usuario,
                "id_estado"=> $request->id_estado,
                "id_prioridad"=> $request->id_prioridad
            ]);

            return ApiResponse::success(
                "Ticket creado con exito",
                201,
                $newTicket->only([
                    "codigo_ticket",
                    "descripcion",
                    "fecha_inicio",
                    "fecha_fin",
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

            // Para ocultar el FKs de la tabla
            $showTicket->makeHidden(["id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at"]);
            // Para ocultar los PKs, FKs y timestamps de las tablas relaciones
            $showTicket->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
            $showTicket->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
            $showTicket->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
            $showTicket->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
            $showTicket->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
            $showTicket->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);

            return ApiResponse::success(
                "Ticket encontrado con exito",
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

    public function update(Request $request, $ticket)
    {
        try {
            $updateTicket = Ticket::findOrFail($ticket);

            $updateTicket->update([
                "codigo_ticket"=> $request->codigo_ticket,
                "descripcion"=> $request->descripcion,
                "fecha_inicio"=> $request->fecha_inicio,
                "fecha_fin"=> $request->fecha_fin,
                "id_categoria"=> $request->id_categoria,
                "id_usuario"=> $request->id_usuario,
                "id_estado"=> $request->id_estado,
                "id_prioridad"=> $request->id_prioridad
            ]);

            return ApiResponse::success(
                "Calificación ticket actualizada con exito",
                200,
                $updateTicket->refresh()->only([
                    "codigo_ticket",
                    "descripcion",
                    "fecha_inicio",
                    "fecha_fin",
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
                "Ticket eliminado con exito",
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
