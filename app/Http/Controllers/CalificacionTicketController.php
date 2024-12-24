<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalificacionTicket\StoreCalificacionTicketRequest;
use App\Http\Requests\CalificacionTicket\UpdateCalificacionTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\CalificacionTicket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CalificacionTicketController extends Controller
{
    public function index()
    {
        try {
            // Relaciones anidadas para con la tabla tickets
            $allQualifications = CalificacionTicket::with("tickets")
                ->with(["tickets.categoria_tickets"])
                ->with(["tickets.usuarios"])
                ->with(["tickets.usuarios.departamentos"])
                ->with(["tickets.usuarios.departamentos.areas"])
                ->with(["tickets.estado_tickets"])
                ->with(["tickets.prioridad_tickets"])
                    ->orderBy("id", "asc")
                    ->paginate(20);

            if ($allQualifications->isEmpty()) {
                return ApiResponse::success(
                    "Lista de calificaciones de ticket vacia",
                    200,
                    $allQualifications
                );
            }

            // Para ocultar el FK de la tabla
            $allQualifications->getCollection()->transform(function ($qualification) {
                $qualification->makeHidden(["id_ticket"]);
                // Para ocultar los PKs, FKs y timestamps de las tablas relaciones
                $qualification->tickets?->makeHidden(["id", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at", "updated_at"]);
                $qualification->tickets?->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
                $qualification->tickets?->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
                $qualification->tickets?->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
                $qualification->tickets?->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
                $qualification->tickets?->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                $qualification->tickets?->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                return $qualification;
            });

            return ApiResponse::success(
                "Listado de calificaciones de ticket",
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

            return ApiResponse::success(
                "Calificación de ticket creada con exito",
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
            $showQualification = CalificacionTicket::with("tickets")
                ->with(["tickets.categoria_tickets"])
                ->with(["tickets.usuarios"])
                ->with(["tickets.usuarios.departamentos"])
                ->with(["tickets.usuarios.departamentos.areas"])
                ->with(["tickets.estado_tickets"])
                ->with(["tickets.prioridad_tickets"])
                    ->findOrFail($calificacionTicket);

            // Para ocultar el FK de la tabla
            $showQualification->makeHidden(["id_ticket"]);
            // Para ocultar los PKs, FKs y timestamps de las tablas relaciones
            $showQualification->tickets?->makeHidden(["id", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at", "updated_at"]);
            $showQualification->tickets?->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
            $showQualification->tickets?->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
            $showQualification->tickets?->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
            $showQualification->tickets?->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
            $showQualification->tickets?->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
            $showQualification->tickets?->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);

            return ApiResponse::success(
                "Calificación ticket encontrada con exito",
                200,
                $showQualification
            );

        } catch(ModelNotFoundException $e) {
            return ApiResponse::error(
                "Calificación ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateCalificacionTicketRequest $request, $calificacionTicket)
    {
        try {
            $updateQualification = CalificacionTicket::findOrFail($calificacionTicket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateQualification->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar calificación ticket",
                    200,
                    array_intersect_key($newData, array_flip([
                        "calificacion",
                        "observacion"
                    ]))
                );
            }
            $updateQualification->update($newData);

            return ApiResponse::success(
                "Calificación ticket actualizada con exito",
                200,
                $updateQualification->refresh()->only([
                    "calificacion",
                    "observacion",
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

    public function destroy($calificacionTicket)
    {
        try {
            CalificacionTicket::findOrFail($calificacionTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Calificación ticket eliminada con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Calificación ticket no encontrada",
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
