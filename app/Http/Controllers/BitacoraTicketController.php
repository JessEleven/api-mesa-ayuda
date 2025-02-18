<?php

namespace App\Http\Controllers;

use App\Http\Requests\BitacoraTicket\StoreBitacoraTicketRequest;
use App\Http\Requests\BitacoraTicket\UpdateBitacoraTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\BitacoraTicket;
use App\Models\TecnicoAsignado;
use App\Models\Ticket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BitacoraTicketController extends Controller
{
    public function index()
    {
        try {
            // Relaciones anidadas para con la tabla tecnico_asignados
            $allLogs = BitacoraTicket::with(["tecnico_asigados.usuarios.departamentos"])
                ->with(["tecnico_asigados.usuarios.departamentos.areas"])
            // Relaciones anidadas para con la tabla tickets
                ->with(["tecnico_asigados.tickets.categoria_tickets"])
                ->with(["tecnico_asigados.tickets.usuarios"])
                ->with(["tecnico_asigados.tickets.usuarios.departamentos"])
                ->with(["tecnico_asigados.tickets.usuarios.departamentos.areas"])
                ->with(["tecnico_asigados.tickets.estado_tickets"])
                ->with(["tecnico_asigados.tickets.prioridad_tickets"])
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

            // Para ocultar el FKs de la tabla
            $allLogs->getCollection()->transform( function($log) {
                $log->makeHidden(["id_tecnico_asignado", "recurso_eliminado"]);
                // Para ocultar los PKs, FKs, algunos campos y timestamps de la tabla relacionada con usuarios
                $log->tecnico_asigados?->makeHidden(["id", "id_usuario", "id_ticket", "recurso_eliminado", "created_at", "updated_at"]);
                $log->tecnico_asigados?->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento","created_at", "updated_at"]);
                $log->tecnico_asigados?->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
                $log->tecnico_asigados?->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
                // Para ocultar los PKs, FKs, algunos campos y timestamps de la tabla relacionada con tickets
                $log->tecnico_asigados?->tickets?->makeHidden(["id", "recurso_eliminado", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at", "updated_at"]);
                $log->tecnico_asigados?->tickets?->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento", "created_at", "updated_at"]);
                $log->tecnico_asigados?->tickets?->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
                $log->tecnico_asigados?->tickets?->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
                $log->tecnico_asigados?->tickets?->categoria_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                $log->tecnico_asigados?->tickets?->estado_tickets?->makeHidden(["id", "color_estado", "orden_prioridad", "created_at", "updated_at"]);
                $log->tecnico_asigados?->tickets?->prioridad_tickets?->makeHidden(["id", "color_prioridad", "orden_prioridad", "created_at", "updated_at"]);
                return $log;
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

    public function show($bitacoraTicket)
    {
        try {
            // Relaciones anidadas para con la tabla tecnico_asignados
            $showLog = BitacoraTicket::with(["tecnico_asigados.usuarios.departamentos"])
                ->with(["tecnico_asigados.usuarios.departamentos.areas"])
            // Relaciones anidadas para con la tabla tickets
                ->with(["tecnico_asigados.tickets.categoria_tickets"])
                ->with(["tecnico_asigados.tickets.usuarios"])
                ->with(["tecnico_asigados.tickets.usuarios.departamentos"])
                ->with(["tecnico_asigados.tickets.usuarios.departamentos.areas"])
                ->with(["tecnico_asigados.tickets.estado_tickets"])
                ->with(["tecnico_asigados.tickets.prioridad_tickets"])
                    ->whereNull("recurso_eliminado")
                    ->findOrFail($bitacoraTicket);

            // Para ocultar el FKs de la tabla
            $showLog->makeHidden(["id_tecnico_asignado", "recurso_eliminado"]);
            // Para ocultar los PKs, FKs, algunos campos y timestamps de la tabla relacionada con usuarios
            $showLog->tecnico_asigados?->makeHidden(["id", "id_usuario", "id_ticket", "recurso_eliminado", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento","created_at", "updated_at"]);
            $showLog->tecnico_asigados?->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
            // Para ocultar los PKs, FKs, algunos campos y timestamps de la tabla relacionada con tickets
            $showLog->tecnico_asigados?->tickets?->makeHidden(["id", "recurso_eliminado", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->tickets?->usuarios?->makeHidden(["id", "telefono", "email", "id_departamento", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->tickets?->usuarios?->departamentos?->makeHidden(["id", "secuencia_departamento", "peso_prioridad", "id_area", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->tickets?->usuarios?->departamentos?->areas?->makeHidden(["id", "secuencia_area", "peso_prioridad", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->tickets?->categoria_tickets?->makeHidden(["id", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->tickets?->estado_tickets?->makeHidden(["id", "color_estado", "orden_prioridad", "created_at", "updated_at"]);
            $showLog->tecnico_asigados?->tickets?->prioridad_tickets?->makeHidden(["id", "color_prioridad", "orden_prioridad", "created_at", "updated_at"]);

            return ApiResponse::show(
                "Bitácora de ticket encontrada con éxito",
                200,
                $showLog
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Bitácora de ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateBitacoraTicketRequest $request, $bitacoraTicket)
    {
        try {
            $updateLog = BitacoraTicket::findOrFail($bitacoraTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateLog->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar la bitácora de ticket",
                    200,
                    array_intersect_key($newData, array_flip([
                        "descripcion"
                    ]))
                );
            }
            $updateLog->update($newData);

            return ApiResponse::updated(
                "Bitácora de ticket actualizada con éxito",
                200,
                $updateLog->refresh()->only([
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

    public function destroy($bitacoraTicket)
    {
        try {
            // Se verifica si la bitácora previamente ha sido eliminado
            $isDeleted = BitacoraTicket::where("id", $bitacoraTicket)
                ->whereNotNull("recurso_eliminado")
                ->exists();

            if ($isDeleted) {
                throw new ModelNotFoundException();
            }
            BitacoraTicket::findOrFail($bitacoraTicket)->delete();

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

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Bitácora no encontrada",
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
