<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalificacionTicket\StoreCalificacionTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\CalificacionTicket;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use App\Services\CalificacionTicketModelHider;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CalificacionTicketController extends Controller
{
    public function index()
    {
        try {
            // Relaciones anidadas para con la tabla tickets
            $allQualifications = CalificacionTicket::with("ticket")
                ->with(["ticket.usuario"])
                ->with(["ticket.usuario.departamento"])
                ->with(["ticket.usuario.departamento.area"])
                ->with(["ticket.categoriaTicket"])
                ->with(["ticket.estadoTicket"])
                ->with(["ticket.prioridadTicket"])
                    ->orderBy("id", "asc")
                    ->paginate(20);

            if ($allQualifications->isEmpty()) {
                return ApiResponse::index(
                    "Lista de calificaciones de tickets vacía",
                    200,
                    $allQualifications
                );
            }

            // Usando el servicio para ocultar los campos
            $allQualifications->getCollection()->transform(function ($qualification) {
                return CalificacionTicketModelHider::hideCalificacionTicketFields($qualification);
            });

            return ApiResponse::index(
                "Lista de calificaciones de tickets",
                200,
                $allQualifications
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreCalificacionTicketRequest $request)
    {
        try {
            $newQualification = CalificacionTicket::create($request->validated());

            return ApiResponse::created(
                "Calificación de ticket creada con éxito",
                201,
                $newQualification->only([
                    "calificacion",
                    "observacion",
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

    public function show($calificacionTicket)
    {
        try {
            // Relaciones anidadas para con la tabla tickets
            $showQualification = CalificacionTicket::with("ticket")
                ->with(["ticket.usuario"])
                ->with(["ticket.usuario.departamento"])
                ->with(["ticket.usuario.departamento.area"])
                ->with(["ticket.categoriaTicket"])
                ->with(["ticket.estadoTicket"])
                ->with(["ticket.prioridadTicket"])
                    ->findOrFail($calificacionTicket);

            // Usando el servicio para ocultar los campos
            CalificacionTicketModelHider::hideCalificacionTicketFields($showQualification);

            return ApiResponse::show(
                "Calificación de ticket encontrada con éxito",
                200,
                $showQualification
            );

        } catch(ModelNotFoundException $e) {
            return ApiResponse::error(
                "Calificación de ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(Request $request, $calificacionTicket)
    {
        try {
            $updateQualification = CalificacionTicket::findOrFail($calificacionTicket);

            return ApiResponse::updated(
                "Calificación de ticket no actualizada",
                400,
                ["created_at"=> $updateQualification->created_at]
            );

        } catch(ModelNotFoundException $e) {
            return ApiResponse::error(
                "Calificación de ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($calificacionTicket)
    {
        try {
            $qualificationId = CalificacionTicket::findOrFail($calificacionTicket);

            $ticketId = Ticket::find($qualificationId->id_ticket);

            // Se busca el estado actual que tiene el ticket
            $currentStatus = EstadoTicket::find($ticketId->id_estado);

            if ($qualificationId) {
                return ApiResponse::error(
                    "El ticket ya está calificado",
                    400,
                    [
                        "current_status"=> $currentStatus->nombre_estado,
                        "ticket_end_date"=> $ticketId->fecha_fin
                    ]
                );
            }

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Calificación de ticket no encontrada",
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
