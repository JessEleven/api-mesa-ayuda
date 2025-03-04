<?php

namespace App\Http\Requests\Area;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\AreaNotFound;
use App\Models\Area;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateAreaRequest extends FormRequest
{
    // Reutilizando el Trait
    use AreaNotFound;

    public function authorize(): bool
    {
        // Uso del Trait
        $this->findAreaOrFail();

        return true;
    }

    public function rules(): array
    {
        // Uso del Trait
        $id = $this->findAreaOrFail();

        $tableName = (new Area())->getTable();

        return [
            "nombre_area"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                Rule::unique($tableName)->ignore($id)
            ],
            "sigla_area"=> [
                "required",
                "regex:/^[A-Z][a-zA-Z]*$/",
                Rule::unique($tableName)->ignore($id)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_area.required"=> "El nombre del área es requerida",
            "nombre_area.regex"=> "Debe ser una cadena de texto",
            "nombre_area.unique"=> "El nombre del área ya está en uso",

            "sigla_area.required"=> "La sigla del área es requerida",
            "sigla_area.regex"=> "Empieza con mayúscula y puede contener minúsculas",
            "sigla_area.unique"=> "La sigla del área ya está en uso"
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
