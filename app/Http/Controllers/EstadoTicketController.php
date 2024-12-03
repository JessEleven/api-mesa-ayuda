<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\EstadoTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class EstadoTicketController extends Controller
{
    private $statusRules = array (
        "nombre_estado"=> "required|regex:/^[a-zA-Z\s]+$/|unique:estado_tickets,nombre_estado",
        "color_estado"=> "required|unique:estado_tickets,color_estado",
        "orden_prioridad"=> "required|integer|min:1|unique:estado_tickets,orden_prioridad",
    );

    private $statusRulesUpdate = array (
        "nombre_estado"=> "required|regex:/^[a-zA-Z\s]+$/|unique:estado_tickets,nombre_estado",
        "color_estado"=> "required|unique:estado_tickets,color_estado"
    );

    private $statusMessages = array (
        "nombre_estado.required"=> "El nombre del estado es requerido",
        "nombre_estado.regex"=> "Debe ser una cadena de texto",
        "nombre_estado.unique"=> "El nombre del estado debe ser único",

        "color_estado.required"=> "El color del estado es requerido",
        "color_estado.unique"=> "El color del estado debe ser único",

        "orden_prioridad.required"=> "La prioridad del estado es requerida",
        "orden_prioridad.integer"=> "Solo se aceptan números enteros",
        "orden_prioridad.min"=> "Solo números enteros mayores o iguales a uno",
        "orden_prioridad.unique"=> "La prioridad del estado debe ser única",
    );

    private $statusMessagesUpdate = array (
        "nombre_estado.required"=> "El nombre del estado es requerido",
        "nombre_estado.regex"=> "Debe ser una cadena de texto",
        "nombre_estado.unique"=> "Ya existe un estado igual",

        "color_estado.required"=> "El color del estado es requerido",
        "color_estado.unique"=> "Ya existe un color de estado igual",
    );

    public function index()
    {
        try {
            $allStatus = EstadoTicket::orderBy("id", "asc")->paginate(20);

            if ($allStatus->isEmpty()) {
                return ApiResponse::success(
                    "Lista de estados de ticket vacia",
                    200
                );
            }
            return ApiResponse::success(
                "Listado de estados de ticket",
                200,
                $allStatus
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
            $validator = Validator::make($request->all(), $this->statusRules, $this->statusMessages);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages" => $messages],422);
            }

            $newStatusTicket = EstadoTicket::create([
                "nombre_estado"=> $request->nombre_estado,
                "color_estado"=> $request->color_estado,
                "orden_prioridad"=> $request->orden_prioridad,
            ]);
            return ApiResponse::success(
                "Estado de ticket creado con exito",
                201,
                $newStatusTicket
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($estadoTicket)
    {
        try {
            $showStatusTicket = EstadoTicket::findOrFail($estadoTicket);

            return ApiResponse::success(
                "Estado ticket encontrado con exito",
                200,
                $showStatusTicket
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Estado ticket no encontrado",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(Request $request, $estadoTicket)
    {
        try {
            $statusTicketExists = EstadoTicket::findOrFail($estadoTicket);

            $validator = Validator::make($request->all(), $this->statusRulesUpdate, $this->statusMessagesUpdate);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages"=> $messages], 422);
            }

            $statusTicketExists->update([
                "nombre_estado"=> $request->nombre_estado,
                "color_estado"=> $request->color_estado
            ]);

            $statusTicketExists->refresh();
            return ApiResponse::success(
                "Estado ticket actualizado con exito",
                200,
                $statusTicketExists
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Estado ticket no encontrado",
                404
            );

        }   catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }


    public function destroy($estadoTicket)
    {
        try {
            EstadoTicket::findOrFail($estadoTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Estado ticket eliminado con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Estado ticket no encontrado",
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
