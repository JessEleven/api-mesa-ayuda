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
                $newTicket
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
        //
    }

    public function destroy($ticket)
    {
        //
    }
}
