<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalificacionTicket\StoreCalificacionTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\CalificacionTicketNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Models\CalificacionTicket;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use App\Services\CalificacionTicketModelHider;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class CalificacionTicketController extends Controller
{
    // Reutilizando los Traits
    use HandlesRequestId;
    use CalificacionTicketNotFound;

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

    public function show()
    {
        try {
            // Uso de los Traits
            $id = $this->validateRequestId();
            $showQualification = $this->findCalificacionTicketOrFail($id);

            // Usando el servicio para ocultar los campos
            CalificacionTicketModelHider::hideCalificacionTicketFields($showQualification);

            return ApiResponse::show(
                "Calificación de ticket encontrada con éxito",
                200,
                $showQualification
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

    public function update()
    {
        try {
            // Uso de los Traits
            $id = $this->validateRequestId();
            $qualificationId = $this->findCalificacionTicketOrFail($id);

            $ticketId = Ticket::find($qualificationId->id_ticket);

            // Se busca el estado actual que tiene el ticket
            $currentStatus = EstadoTicket::find($ticketId->id_estado);

            return ApiResponse::error(
                "La calificación de ticket no se actualizada",
                400,
                [
                    "current_status"=> $currentStatus->nombre_estado,
                    "qualification_created"=> $qualificationId->created_at
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

    public function destroy()
    {
        try {
            // Uso de los Traits
            $id = $this->validateRequestId();
            $qualificationId = $this->findCalificacionTicketOrFail($id);

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

        } catch (HttpResponseException $e) {
            return $e->getResponse();

        }  catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }
}
