<?php

namespace App\Http\Controllers;

use App\Http\Requests\TecnicoAsignado\StoreTecnicoAsignadoRequest;
use App\Http\Requests\TecnicoAsignado\UpdateTecnicoAsignadoRequest;
use App\Http\Responses\ApiResponse;
use App\Models\TecnicoAsignado;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TecnicoAsignadoController extends Controller
{
    public function index()
    {
        try {
            // Relaciones anidadas para con la tabla usuarios
            $allTechnicians = TecnicoAsignado::with("usuarios")
                ->with(["usuarios.departamentos"])
                ->with(["usuarios.departamentos.areas"])
            // Relaciones anidadas para con la tabla tickets
                    ->with(["tickets.categoria_tickets"])
                    ->with(["tickets.usuarios"])
                    ->with(["tickets.usuarios.departamentos"])
                    ->with(["tickets.usuarios.departamentos.areas"])
                    ->with(["tickets.estado_tickets"])
                    ->with(["tickets.prioridad_tickets"])
                        ->orderBy("id", "asc")
                        ->paginate("20");

            // Para ocultar el FKs de la tabla
            $allTechnicians->getCollection()->transform( function($technician) {
                $technician->makeHidden(["id_usuario", "id_ticket"]);
                // Para ocultar los PKs, FKs y timestamps de la tabla relacionada con usuarios
                $technician->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
                $technician->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
                $technician->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
                // Para ocultar los PKs, FKs y timestamps de la tabla relacionada con tickets
                $technician->tickets?->makeHidden(["id", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at", "updated_at"]);
                $technician->tickets?->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
                $technician->tickets?->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
                $technician->tickets?->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
                $technician->tickets?->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
                $technician->tickets?->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                $technician->tickets?->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);
                return $technician;
            });

            if ($allTechnicians->isEmpty()) {
                return ApiResponse::success(
                    "Lista de técnicos asignado vacía",
                    200,
                    $allTechnicians
                );
            }
            return ApiResponse::success(
                "Listado de técnicos asignado",
                200,
                $allTechnicians
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreTecnicoAsignadoRequest $request)
    {
        try {
            $newTechnician = TecnicoAsignado::create($request->validated());

            return ApiResponse::success(
                "Técnico asignado creado con éxito",
                201,
                $newTechnician->only([
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

    public function show($tecnicoAsignado)
    {
        try {
            // Relaciones anidadas para con la tabla usuarios
            $showTechnician = TecnicoAsignado::with("usuarios")
                ->with(["usuarios.departamentos"])
                ->with(["usuarios.departamentos.areas"])
            // Relaciones anidadas para con la tabla tickets
                    ->with(["tickets.categoria_tickets"])
                    ->with(["tickets.usuarios"])
                    ->with(["tickets.usuarios.departamentos"])
                    ->with(["tickets.usuarios.departamentos.areas"])
                    ->with(["tickets.estado_tickets"])
                    ->with(["tickets.prioridad_tickets"])
                        ->orderBy("id", "asc")
                        ->findOrFail($tecnicoAsignado);

            // Para ocultar el FKs de la tabla
            $showTechnician->makeHidden(["id_usuario", "id_ticket"]);
            // Para ocultar los PKs, FKs y timestamps de la tabla relacionada con usuarios
            $showTechnician->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
            $showTechnician->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
            $showTechnician->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
            // Para ocultar los PKs, FKs y timestamps de la tabla relacionada con tickets
            $showTechnician->tickets?->makeHidden(["id", "id_categoria", "id_usuario", "id_estado", "id_prioridad", "created_at", "updated_at"]);
            $showTechnician->tickets?->usuarios?->makeHidden(["id", "id_departamento", "created_at", "updated_at"]);
            $showTechnician->tickets?->usuarios?->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
            $showTechnician->tickets?->usuarios?->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
            $showTechnician->tickets?->categoria_tickets?->makeHidden(["id","created_at", "updated_at"]);
            $showTechnician->tickets?->estado_tickets?->makeHidden(["id", "created_at", "updated_at"]);
            $showTechnician->tickets?->prioridad_tickets?->makeHidden(["id", "created_at", "updated_at"]);

            return ApiResponse::success(
                "Técnico asignado encontrado con éxito",
                200,
                $showTechnician
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Técnico asignado no encontrado",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateTecnicoAsignadoRequest $request, $tecnicoAsignado)
    {
        try {
            $updateTechnician = TecnicoAsignado::findOrFail($tecnicoAsignado);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateTechnician->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar técnico asignado",
                    200,
                    array_intersect_key($newData, array_flip([
                        "id_usuario",
                        "id_ticket"
                    ]))
                );
            }
            $updateTechnician->update($newData);

            return ApiResponse::success(
                "Técnico asignado actualizado con éxito",
                200,
                $updateTechnician->refresh()->only([
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

    public function destroy($tecnicoAsignado)
    {
        try {
            TecnicoAsignado::findOrFail($tecnicoAsignado)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Técnico asignado eliminado con éxito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Técnico asignado no encontrado",
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
