<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\BitacoraTicket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BitacoraTicketController extends Controller
{
    public function index()
    {
        try {
            $allLogs = BitacoraTicket::with("tecnico_asigados")
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

    public function store(Request $request)
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
            $showLog = BitacoraTicket::with("tecnico_asigados")->findOrFail($bitacoraTicket);

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

    public function update(Request $request, $bitacoraTicket)
    {
        try {
            $updateLog = BitacoraTicket::findOrFail($bitacoraTicket);

            $updateLog->update([
                "descripcion"=> $request->descripcion,
                "id_tecnico_asignado"=> $request->id_tecnico_asignado
            ]);

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
