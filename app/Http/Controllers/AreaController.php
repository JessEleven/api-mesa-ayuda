<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    private $areaRules = array(
        "nombre_area"=> "required|regex:/^[a-zA-Z\s]+$/|unique:areas,nombre_area",
        "peso_prioridad"=> "required|integer|min:1|unique:areas,peso_prioridad",
    );

    private $areaRulesUpdate = array(
        "nombre_area"=> "required|regex:/^[a-zA-Z\s]+$/|unique:areas,nombre_area"
    );

    private $areaMessages = array(
        "nombre_area.required"=> "El nombre del área es requerida",
        "nombre_area.regex"=> "Debe ser una cadena de texto",
        "nombre_area.unique"=> "El área debe ser única",

        "peso_prioridad.required"=> "La prioridad de área es requerida",
        "peso_prioridad.integer"=> "Solo se aceptan números enteros",
        "peso_prioridad.min"=> "Solo números enteros mayores o iguales a uno",
        "peso_prioridad.unique"=> "La prioridad del área es única"
    );

    private $areaMessagesUpdate = array(
        "nombre_area.required"=> "El nombre del área es requerida",
        "nombre_area.regex"=> "Debe ser una cadena de texto",
        "nombre_area.unique"=> "Ya exite una área igual",
    );

    public function index()
    {
        try {
            $allAreas = Area::all();

            if ($allAreas->isEmpty()) {
                return response()->json([
                    "message"=> "No se encontraron áreas"
                ], 200);
            }
            return response()->json([
                "description"=> "Total de áreas: ". $allAreas->count(),
                "data"=> $allAreas
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
            $validator = Validator::make($request->all(), $this->areaRules, $this->areaMessages);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages"=> $messages], 422);
            }

            $newArea = Area::create([
                "nombre_area" => $request->nombre_area,
                "peso_prioridad"=> $request->peso_prioridad,
            ]);
            return response()->json([
                "success"=> "Area creada con exito",
                "data"=> $newArea
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function show($area)
    {
        try {
            $showArea = Area::findOrFail($area);

            return response()->json([
                "message"=> "Área encontrada con exito",
                "data"=> $showArea
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Área no encontrada"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function update(Request $request, $area)
    {
        try {
            $areaExists = Area::findOrFail($area);

            $validator = Validator::make($request->all(), $this->areaRulesUpdate, $this->areaMessagesUpdate);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages"=> $messages], 422);
            }

            $areaExists->update([
                "nombre_area" => $request->nombre_area
            ]);

            $areaExists->refresh();
            return response()->json([
                "success"=> "Área actualizada con exito",
                "data"=> $areaExists
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Área no encontrada"
            ], 404);
        }

        catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function destroy($area)
    {
        try {
            Area::findOrFail($area)->delete();
            return response()->json([
                "success"=> "Área eliminada con exito"
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Área no encontrada"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }
}
