<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalificacionTicket\StoreCalificacionTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\CalificacionTicketNotFound;
use App\Http\Traits\HandlesServerErrors\HandleException;
use App\Models\CalificacionTicket;
use App\Services\CalificacionTicketModelHider;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class CalificacionTicketController extends Controller
{
    // Reutilizando los Traits
    use CalificacionTicketNotFound;
    use HandleException;

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

            // Uso del Service (app/Services/CalificacionTicketModelHider) para ocultar los campos
            $allQualifications->getCollection()->transform(function ($qualification) {
                return CalificacionTicketModelHider::hideCalificacionTicketFields($qualification);
            });

            return ApiResponse::index(
                "Lista de calificaciones de tickets",
                200,
                $allQualifications
            );

        // Uso del Trait en index, store, show, update y destroy
        } catch (Exception $e) {
            return $this->isAnException($e);
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
            return $this->isAnException($e);
        }
    }

    public function show()
    {
        try {
            // Uso del Trait
            $showQualification = $this->findCalificacionTicketOrFail();

            // Uso del Service (app/Services/CalificacionTicketModelHider) para ocultar los campos
            CalificacionTicketModelHider::hideCalificacionTicketFields($showQualification);

            return ApiResponse::show(
                "Calificación de ticket encontrada con éxito",
                200,
                $showQualification
            );

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }

    public function update()
    {
        try {
            // Uso de los Traits
            $this->findCalificacionTicketOrFail();
            $this->ticketQualifiedAt("update");

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }

    public function destroy()
    {
        try {
            // Uso de los Traits
            $this->findCalificacionTicketOrFail();
            $this->ticketQualifiedAt("destroy");

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        }  catch (Exception $e) {
            return $this->isAnException($e);
        }
    }
}
