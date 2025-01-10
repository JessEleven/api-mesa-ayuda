<?php

namespace App\Http\Controllers;

use App\Http\Requests\BitacoraTicket\StoreBitacoraTicketRequest;
use App\Http\Requests\BitacoraTicket\UpdateBitacoraTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Models\BitacoraTicket;
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
                        ->orderBy("id", "asc")
                        ->paginate(20);

            if ($allLogs->isEmpty()) {
                return ApiResponse::success(
                    "Listado de bitácoras vacía",
                    200,
                    $allLogs
                );
            }

            return ApiResponse::success(
                "Listado de bitácoras",
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
            $newLog = BitacoraTicket::create([
                "descripcion"=> $request->descripcion,
                "id_tecnico_asignado"=> $request->id_tecnico_asignado
            ]);

            return ApiResponse::success(
                "Bitácora creada con éxito",
                201,
                $newLog->only([
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
                        ->findOrFail($bitacoraTicket);

            return ApiResponse::success(
                "Bitácora encontrada con éxito",
                200,
                $showLog
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
                return ApiResponse::success(
                    "No hay cambios para actualizar bitácora",
                    200,
                    array_intersect_key($newData, array_flip([
                        "descripcion"
                    ]))
                );
            }
            $updateLog->update($newData);

            return ApiResponse::success(
                "Bitácora actualizada con éxito",
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
            BitacoraTicket::findOrFail($bitacoraTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Bitácora eliminada con éxito",
                200,
                ["related"=> $baseRoute]
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
