<?php

namespace App\Http\Controllers;

use App\Models\EstadoTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            $allStatus = EstadoTicket::all();

            if ($allStatus->isEmpty()) {
                return response()->json([
                    "message"=> "No se encontraron estados de ticket"
                ], 200);
            }
            return response()->json([
                "description"=> "Total de estados de ticket: ". $allStatus->count(),
                "data"=> $allStatus
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
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
            return response()->json([
                "success"=> "Estado de ticket creado con exito",
                "data"=> $newStatusTicket
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function show($estadoTicket)
    {
        try {
            $showStatusTicket = EstadoTicket::findOrFail($estadoTicket);

            return response()->json([
                "success"=> "Estado ticket encontrado con exito",
                "data"=> $showStatusTicket
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Estado ticket no encontrado"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
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
            return response()->json([
                "success"=> "Estado ticket actualizado con exito",
                "data"=> $statusTicketExists
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Estado ticket no encontrado"
            ], 404);

        }   catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }


    public function destroy($estadoTicket)
    {
        try {
            EstadoTicket::findOrFail($estadoTicket)->delete();
            return response()->json([
                "success"=> "Estado ticket eliminado con exito"
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Estado ticket no encontrado"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
             ], 500);
        }
    }
}
