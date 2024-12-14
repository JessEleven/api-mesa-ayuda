<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Exception;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        try {
            $allTickets = Ticket::with("usuarios", "categoria_tickets",
            "estado_tickets", "prioridad_tickets", "calificacion_tickets")
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
                "id_prioridad"=> $request->id_prioridad,
                "id_calificacion"=> $request->id_calificacion
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

    public function show(Ticket $ticket)
    {
        //
    }

    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    public function destroy(Ticket $ticket)
    {
        //
    }
}
