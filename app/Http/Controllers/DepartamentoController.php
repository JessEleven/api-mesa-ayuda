<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class DepartamentoController extends Controller
{
    private $departmentRules = array(
        "nombre_departamento"=> "required|regex:/^[a-zA-Z\s]+$/|unique:departamentos,nombre_departamento",
        "peso_prioridad"=> "required|integer|min:1|unique:departamentos,peso_prioridad",
    );

    private $departmentRulesUpdate = array(
        "nombre_departamento"=> "required|regex:/^[a-zA-Z\s]+$/|unique:departamentos,nombre_departamento",
    );

    private $departmentMessages = array(
        "nombre_departamento.required"=> "El nombre del departamento es requerido",
        "nombre_departamento.regex"=> "Debe ser una cadena de texto",
        "nombre_departamento.unique"=> "El nombre del departamento debe ser único",

        "peso_prioridad.required"=> "La prioridad del departamento es requerida",
        "peso_prioridad.integer"=> "Solo se aceptan números enteros",
        "peso_prioridad.min"=> "Solo números enteros mayores o iguales a uno",
        "peso_prioridad.unique"=> "La prioridad del departamento es única"
    );

    private $departmentMessagesUpdate = array(
        "nombre_departamento.required"=> "El nombre del departamento es requerido",
        "nombre_departamento.string"=> "Debe ser una cadena de texto",
        "nombre_departamento.unique"=> "Ya exite un departamento igual",
    );

    public function index()
    {
        try {
            $allDepartments = Departamento::orderBy("id","asc")->paginate(20);

            if ($allDepartments->isEmpty()) {
                return ApiResponse::success(
                    "Lista de departamentos vacia",
                    200
                );
            }
            return ApiResponse::success(
                "Listado de departamentos",
                200,
                $allDepartments
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
            $validator = Validator::make($request->all(), $this->departmentRules, $this->departmentMessages);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages"=> $messages], 422);
            }

            $newDepartment = Departamento::create([
                "nombre_departamento"=> $request->nombre_departamento,
                "peso_prioridad"=> $request->peso_prioridad,
            ]);
            return ApiResponse::success(
                "Departamento creado con exito",
                201,
                $newDepartment
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($departamento)
    {
        try {
            $showDepartment = Departamento::findOrFail($departamento);

            return ApiResponse::success(
                "Departamento encontrado con exito",
                200,
                $showDepartment
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Departamento no encontrado",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(Request $request, $departamento)
    {
        try {
            $departmentExists = Departamento::findOrFail($departamento);

            $validator = Validator::make($request->all(), $this->departmentRulesUpdate, $this->departmentMessagesUpdate);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages"=> $messages], 422);
            }

            $departmentExists->update([
                "nombre_departamento"=> $request->nombre_departamento
            ]);

            $departmentExists->refresh();
            return ApiResponse::success(
                "Departamento actualizado con exito",
                200,
                $departmentExists
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Departamento no encontrado",
                404
            );
        }

        catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($departamento)
    {
        try {
            Departamento::findOrFail($departamento)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Departamento eliminado con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Departamento no encontrado",
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
