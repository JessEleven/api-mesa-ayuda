<?php

namespace App\Http\Controllers;

use App\Http\Requests\BitacoraTicket\StoreBitacoraTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\BitacoraTicketNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Models\BitacoraTicket;
use App\Models\TecnicoAsignado;
use App\Models\Ticket;
use App\Services\BitacoraTicketModelHider;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class BitacoraTicketController extends Controller
{
    // Reutilizando los Traits
    use HandlesRequestId;
    use BitacoraTicketNotFound;

    public function index()
    {
        try {
            // Relaciones anidadas para con la tabla tecnico_asignados
            $allLogs = BitacoraTicket::with(["tecnicoAsignado.usuario.departamento"])
                ->with(["tecnicoAsignado.usuario.departamento.area"])
            // Relaciones anidadas para con la tabla tickets
                ->with(["tecnicoAsignado.ticket.usuario"])
                ->with(["tecnicoAsignado.ticket.usuario.departamento"])
                ->with(["tecnicoAsignado.ticket.usuario.departamento.area"])
                ->with(["tecnicoAsignado.ticket.categoriaTicket"])
                ->with(["tecnicoAsignado.ticket.estadoTicket"])
                ->with(["tecnicoAsignado.ticket.prioridadTicket"])
                    ->whereNull("recurso_eliminado")
                    ->orderBy("id", "asc")
                    ->paginate(20);

            if ($allLogs->isEmpty()) {
                return ApiResponse::index(
                    "Lista de bitácoras de tickets vacía",
                    200,
                    $allLogs
                );
            }

            // Usando el servicio para ocultar los campos
            $allLogs->getCollection()->transform( function($log) {
                return BitacoraTicketModelHider::hideBitacoraTicketFields($log);
            });

            return ApiResponse::index(
                "Lista de bitácoras de tickets",
                200,
                $allLogs
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreBitacoraTicketRequest $request)
    {
        try {
            // Se busca el técnico asiganado
            $tecnico = TecnicoAsignado::find($request->id_tecnico_asignado);

            // Se busca el ticket del técnico asignado
            $ticket = Ticket::find($tecnico->id_ticket);

            // Se extrae el código del ticket para crear el código de la bitácora
            $codigoTicket = $ticket->codigo_ticket;

            $newLog = BitacoraTicket::create(array_merge(
                $request->validated(), [
                    "codigo_bitacora"=> $codigoTicket,
                    "estado_bitacora"=> "Rechazado",
                    "color_bitacora"=> "#ef4444"
                ]
            ));

            return ApiResponse::created(
                "Bitácora de ticket creada con éxito",
                201,
                $newLog->only([
                    "codigo_bitacora",
                    "estado_bitacora",
                    "color_bitacora",
                    "descripcion",
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
            $showLog = $this->findBitacoraTicketOrFail($id);

            // Usando el servicio para ocultar los campos
            BitacoraTicketModelHider::hideBitacoraTicketFields($showLog);

            return ApiResponse::show(
                "Bitácora de ticket encontrada con éxito",
                200,
                $showLog
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
            $updateLog = $this->findBitacoraTicketOrFail($id);

            return ApiResponse::error(
                "La bitácora de ticket no se actualiza",
                400,
                [
                    "current_status"=> $updateLog->estado_bitacora,
                    "binnacle_created"=> $updateLog->created_at
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

            // Se verifica si la bitácora previamente ha sido eliminado
            $isDeleted = BitacoraTicket::where("id", $id)
                ->whereNotNull("recurso_eliminado")
                ->exists();

            if ($isDeleted) {
                return ApiResponse::error(
                    "La bitácora de ticket ya no existe",
                    404
                );
            }
            $deleteLog = $this->findBitacoraTicketOrFail($id);
            $deleteLog->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Bitácora de ticket eliminada con éxito",
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
