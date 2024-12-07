<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departamento\StoreDepartamentoRequest;
use App\Http\Requests\Departamento\UpdateDepartamentoRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Departamento;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DepartamentoController extends Controller
{
    public function index()
    {
        try {
            $allDepartments = Departamento::orderBy("id","asc")->paginate(20);

            if ($allDepartments->isEmpty()) {
                return ApiResponse::success(
                    "Lista de departamentos vacia",
                    200,
                    $allDepartments
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

    public function store(StoreDepartamentoRequest $request)
    {
        try {
            $newDepartment = Departamento::create($request->validated());

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

    public function update(UpdateDepartamentoRequest $request, $departamento)
    {
        try {
            $updateDepartment = Departamento::findOrFail($departamento);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateDepartment->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar departamento",
                    200,
                    $newData
                );
            }
            $updateDepartment->update($newData);

            return ApiResponse::success(
                "Departamento actualizado con exito",
                200,
                $updateDepartment->refresh()
            );

        } catch (Exception $e) {
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
