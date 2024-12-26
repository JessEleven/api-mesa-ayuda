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
            $allDepartments = Departamento::with("areas")
                ->orderBy("id","asc")
                ->paginate(20);

            if ($allDepartments->isEmpty()) {
                return ApiResponse::success(
                    "Lista de departamentos vacia",
                    200,
                    $allDepartments
                );
            }

            // Para ocultar el FK de la tabla
            $allDepartments->getCollection()->transform(function ($department) {
                $department->makeHidden(["id_area"]);
                // Para ocultar el PK y timestamps de la tabla areas
                $department->areas?->makeHidden(["id", "created_at", "updated_at"]);
                return $department;
            });

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
            $validatedData = $request->validated();

            $areaId = $validatedData["id_area"];

            // Se obtiene primero la última secuencia dentro del área
            $lastSequence = Departamento::where("id_area", $areaId)
                ->orderBy("id", "desc")
                ->value("secuencia_departamento");

            if ($lastSequence) {
                // Se extrae el número secuencial global
                $sequenceNumber = (int) substr($lastSequence, 0, 3);
                $sequenceNumber++;
            } else {
                // Si no existe, se inicializa la secuencia en 1
                $sequenceNumber = 1;
            }

            // Se formatea la secuencia a 3 dígitos y se concatena con la sigla
            $secuencia = sprintf("%03d-%s", $sequenceNumber, $validatedData["sigla_departamento"]);

            // Se agrega la secuencia generada a los datos validados
            $validatedData["secuencia_departamento"] = $secuencia;

            $newDepartment = Departamento::create($validatedData);


            return ApiResponse::success(
                "Departamento creado con exito",
                201,
                $newDepartment->only([
                    "nombre_departamento",
                    "sigla_departamento",
                    "secuencia_departamento",
                    "peso_prioridad",
                    "created_at",
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
            $showDepartment = Departamento::with("areas")
                ->findOrFail($departamento);

            // Para ocultar el FK de la tabla
            $showDepartment->makeHidden(["id_area"]);
            // Para ocultar el PK y timestamps de la tabla areas
            $showDepartment->areas?->makeHidden(["id", "created_at", "updated_at"]);

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
                    array_intersect_key($newData, array_flip([
                        "nombre_departamento",
                        "sigla_departamento"
                    ]))
                );
            }

            // Se verifica si la sigla ha cambiado
            if (isset($newData["sigla_departamento"]) && $newData["sigla_departamento"] !== $updateDepartment->sigla_departamento) {

                // Si ha cambiado, se conserva la secuencia y se actualiza solo la sigla
                $currentSequence = $updateDepartment->secuencia_departamento;

                // Se extrae la parte numérica de la secuencia actual
                $sequenceNumber = substr($currentSequence, 0, 3);

                // Se crea la nueva secuencia concatenando el número de la secuencia con la nueva sigla
                $newData["secuencia_departamento"] = sprintf("%s-%s", $sequenceNumber, $newData["sigla_departamento"]);
            }
            $updateDepartment->update($newData);

            return ApiResponse::success(
                "Departamento actualizado con exito",
                200,
                $updateDepartment->refresh()->only([
                    "nombre_departamento",
                    "sigla_departamento",
                    "secuencia_departamento",
                    "created_at",
                    "updated_at",
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
