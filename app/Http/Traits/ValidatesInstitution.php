<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use App\Models\Departamento;
use App\Models\Usuario;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ValidatesInstitution
{
    protected function areaIsActive($area)
    {
        $totalDepartments = Departamento::where("id_area", $area->id)->count();

        if ($totalDepartments > 0) {
            $name = $area->nombre_area;
            $sigla = $area->sigla_area;
            $sequence = $area->secuencia_area;

            $op = $totalDepartments === 1
                ? "departamento asociado"
                : "departamentos asociados";

            throw new HttpResponseException(ApiResponse::error(
                "El área tiene {$totalDepartments} {$op}",
                400,
                [
                    "area_name"=> $name,
                    "area_acronym"=> $sigla,
                    "area_sequence"=> $sequence,
                    "total_associated_departments"=> $totalDepartments
                ]
            ));
        }
    }

    protected function departmentIsActive($departamento)
    {
        $totalUsers = Usuario::where("id_departamento", $departamento->id)->count();

        if ($totalUsers > 0) {
            $name = $departamento->nombre_departamento;
            $sigla = $departamento->sigla_departamento;
            $sequence = $departamento->secuencia_departamento;

            $op = $totalUsers === 1
                ? "usuario asociado"
                : "usuarios asociados";

            throw new HttpResponseException(ApiResponse::error(
                "El departamento tiene {$totalUsers} {$op}",
                400,
                [
                    "department_name"=> $name,
                    "department_acronym"=> $sigla,
                    "department_sequence"=> $sequence,
                    "total_associated_users"=> $totalUsers
                ]
            ));
        }
    }
}
