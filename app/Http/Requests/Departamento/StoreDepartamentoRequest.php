<?php

namespace App\Http\Requests\Departamento;

use App\Http\Responses\ApiResponse;
use App\Models\Area;
use App\Models\Departamento;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreDepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tableDepartment = (new Departamento())->getTable();
        $tableArea = (new Area())->getTable();

        return [
            "nombre_departamento"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                "unique:" . $tableDepartment . ",nombre_departamento"
            ],
            "sigla_departamento"=> [
                "required",
                "regex:/^[A-Z][a-zA-Z]*$/",
                "unique:" . $tableDepartment . ",sigla_departamento"
            ],
            "peso_prioridad"=> [
                "required",
                "integer",
                "min:1",
                Rule::unique($tableDepartment, "peso_prioridad")
                    ->where("id_area", $this->input("id_area"))
            ],
            "id_area"=> [
                "required",
                "exists:" . $tableArea . ",id"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_departamento.required"=> "El nombre del departamento es requerido",
            "nombre_departamento.regex"=> "Debe ser una cadena de texto",
            "nombre_departamento.unique"=> "El nombre del departamento ya está registrado",

            "sigla_departamento.required"=> "La sigla del departamento es requerida",
            "sigla_departamento.regex"=> "Empieza con mayúscula y puede contener minúsculas",
            "sigla_departamento.unique"=> "La sigla del departamento ya está registrado",

            "peso_prioridad.required"=> "La prioridad del departamento es requerida",
            "peso_prioridad.integer"=> "Solo se aceptan números enteros",
            "peso_prioridad.min"=> "Solo números enteros mayores o iguales a uno",
            "peso_prioridad.unique"=> "La prioridad del departamento ya está registrado",

            "id_area.required"=> "El área es requerida",
            "id_area.exists"=> "El área ingresada no existe"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $errorsCount = count($errors);

        $errorMessage = $errorsCount === 1
            ? "Se produjo un error de validación"
            : "Se produjeron varios errores de validación";

        throw new HttpResponseException(ApiResponse::validation(
                $errorMessage,
                422,
                $errors
            )
        );
    }
}
