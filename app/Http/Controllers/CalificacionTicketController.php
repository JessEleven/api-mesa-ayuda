<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\CalificacionTicket;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CalificacionTicketController extends Controller
{
    public function index()
    {
        try {
            $allQualification = CalificacionTicket::orderBy("id", "asc")->paginate(20);

            if ($allQualification->isEmpty()) {
                return ApiResponse::success(
                    "Lista de calificaciones de ticket vacia",
                    200,
                    $allQualification
                );
            }
            return ApiResponse::success(
                "Listado de calificaciones de ticket",
                200,
                $allQualification
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
            $newQualificationTicket = CalificacionTicket::create([
                "calificacion"=> $request->calificacion,
                "observacion"=> $request->observacion
            ]);

            return ApiResponse::success(
                "Calificación de ticket creada con exito",
                201,
                $newQualificationTicket
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
            $showQualificationTicket = CalificacionTicket::findOrFail($calificacionTicket);

            return ApiResponse::success(
                "Calificación ticket encontrada con exito",
                200,
                $showQualificationTicket
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

    public function update(Request $request, $calificacionTicket)
    {
        try {
            $updateQualificationTicket = CalificacionTicket::findOrFail($calificacionTicket);

            $updateQualificationTicket->update([
                "calificacion"=> $request->calificacion,
                "observacion"=> $request->observacion
            ]);

            return ApiResponse::success(
                "Calificación ticket actualizada con exito",
                200,
                $updateQualificationTicket
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
