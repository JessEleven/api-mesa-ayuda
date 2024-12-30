<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\TecnicoAsignado;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TecnicoAsignadoController extends Controller
{
    public function index()
    {
        try {
            $allTechnicians = TecnicoAsignado::with("usuarios")
                ->with("tickets")
                    ->get();

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

    public function store(Request $request)
    {
        try {
            $newTechnician = TecnicoAsignado::create([
                "id_usuario"=> $request->id_usuario,
                "id_ticket"=> $request->id_ticket
            ]);

            return ApiResponse::success(
                "Técnico asignado creado con éxito",
                201,
                $newTechnician
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
            $showTechnician = TecnicoAsignado::with("usuarios")
                ->with("tickets")
                    ->findOrFail($tecnicoAsignado);

            return ApiResponse::success(
                "Técnico asignado encontrado con éxito",
                201,
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

    public function update(Request $request, $tecnicoAsignado)
    {
        try {
            $updateTechnician = TecnicoAsignado::findOrFail($tecnicoAsignado);

            $updateTechnician->update([
                "id_usuario"=> $request->id_usuario,
                "id_ticket"=> $request->id_ticket
            ]);

            return ApiResponse::success(
                "Técnico asignado actualizado con éxito",
                200,
                $updateTechnician
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
