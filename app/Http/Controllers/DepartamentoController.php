<?php

namespace App\Http\Controllers;

use App\Helpers\SecuenciaDepartamentoHelper;
use App\Http\Requests\Departamento\StoreDepartamentoRequest;
use App\Http\Requests\Departamento\UpdateDepartamentoRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Departamento;
use App\Services\DepartamentoModelHider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DepartamentoController extends Controller
{
    public function index()
    {
        try {
            $allDepartments = Departamento::with("area")
                ->orderBy("id","asc")
                ->paginate(20);

            if ($allDepartments->isEmpty()) {
                return ApiResponse::index(
                    "Lista de departamentos vacía",
                    200,
                    $allDepartments
                );
            }

            // Usando el servicio para ocultar los campos
            $allDepartments->getCollection()->transform(function ($department) {
                return DepartamentoModelHider::hideDepartamentoFields($department);
            });

            return ApiResponse::index(
                "Lista de departamentos",
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
            // Se obtiene la secuencia generada usando el helper
            $secuenciaDepartamento = SecuenciaDepartamentoHelper::generateSequenceDepartment(
                $request->id_area,
                $request->sigla_departamento
            );

            $newDepartment = Departamento::create(array_merge(
                $request->validated(), [
                    "secuencia_departamento"=> $secuenciaDepartamento
                ]
            ));

            return ApiResponse::created(
                "Departamento creado con éxito",
                201,
                $newDepartment->only([
                    "nombre_departamento",
                    "sigla_departamento",
                    "secuencia_departamento",
                    "peso_prioridad",
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

    public function show($departamento)
    {
        try {
            $showDepartment = Departamento::with("area")
                ->findOrFail($departamento);

            // Usando el servicio para ocultar los campos
            DepartamentoModelHider::hideDepartamentoFields($showDepartment);

            return ApiResponse::show(
                "Departamento encontrado con éxito",
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
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar departamento",
                    200,
                    array_intersect_key($newData, array_flip([
                        "nombre_departamento",
                        "sigla_departamento"
                    ]))
                );
            }

            // Se verifica si la sigla ha cambiado
            if (isset($newData["sigla_departamento"]) && $newData["sigla_departamento"] !== $updateDepartment->sigla_departamento) {
                // Si ha cambiado, se actualiza la secuencia usando el helper
                $newData["secuencia_departamento"] = SecuenciaDepartamentoHelper::updateSequenceDepartment(
                    $updateDepartment,
                    $newData["sigla_departamento"]
                );
            }
            $updateDepartment->update($newData);

            return ApiResponse::updated(
                "Departamento actualizado con éxito",
                200,
                $updateDepartment->refresh()->only([
                    "nombre_departamento",
                    "sigla_departamento",
                    "secuencia_departamento",
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

    public function destroy($departamento)
    {
        try {
            Departamento::findOrFail($departamento)->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Departamento eliminado con éxito",
                200,
                [
                    "related"=> $relativePath,
                    "api_version"=> $apiVersion
                ]
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
