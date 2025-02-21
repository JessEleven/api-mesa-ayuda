<?php

namespace App\Http\Requests\Departamento;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\DepartamentoNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Models\Area;
use App\Models\Departamento;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateDepartamentoRequest extends FormRequest
{
    // Reutilizando los Traits
    use HandlesRequestId;
    use DepartamentoNotFound;

    public function authorize(): bool
    {
        // Uso de los Traits
        $id = $this->validateRequestId();

        $this->findDepartamentoOrFail($id);

        return true;
    }

    public function rules(): array
    {
        // Uso del Trait
        $id = $this->validateRequestId();

        $tableDepartment = (new Departamento())->getTable();
        $tableArea = (new Area())->getTable();

        return [
            "nombre_departamento"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                Rule::unique($tableDepartment)->ignore($id)
            ],
            "sigla_departamento"=> [
                "required",
                "regex:/^[A-Z][a-zA-Z]*$/",
                Rule::unique($tableDepartment)->ignore($id)
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
            "nombre_departamento.unique"=> "El nombre del departamento ya exite",

            "sigla_departamento.required"=> "La sigla del departamento es requerida",
            "sigla_departamento.regex"=> "Empieza con mayúscula y puede contener minúsculas",
            "sigla_departamento.unique"=> "La sigla del departamento ya existe",

            "id_area.required"=> "El área es requerido",
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
