<?php

namespace App\Http\Requests\Area;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreAreaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "nombre_area"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                "unique:areas,nombre_area"
            ],
            "sigla_area"=> [
                "required",
                "regex:/^[A-Z][a-zA-Z]*$/",
                "unique:areas,sigla_area"
            ],
            "peso_prioridad"=> [
                "required",
                "integer",
                "min:1",
                "unique:areas,peso_prioridad"
            ],
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_area.required"=> "El nombre del área es requerida",
            "nombre_area.regex"=> "Debe ser una cadena de texto",
            "nombre_area.unique"=> "El nombre del área debe ser única",

            "sigla_area.required"=> "La sigla del área es requerida",
            "sigla_area.regex"=> "Empieza con mayúscula y puede contener minúsculas",
            "sigla_area.unique"=> "La sigla del área deber ser única",

            "peso_prioridad.required"=> "La prioridad de área es requerida",
            "peso_prioridad.integer"=> "Solo se aceptan números enteros",
            "peso_prioridad.min"=> "Solo números enteros mayores o iguales a uno",
            "peso_prioridad.unique"=> "La prioridad del área debe ser única"
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
            $errors)
        );
    }
}
