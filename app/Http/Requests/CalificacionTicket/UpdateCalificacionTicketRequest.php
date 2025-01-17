<?php

namespace App\Http\Requests\CalificacionTicket;

use App\Http\Responses\ApiResponse;
use App\Models\CalificacionTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateCalificacionTicketRequest extends FormRequest
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
        // Se obtiene el nombre del parámetro dinámico de la ruta
        $routeName = $this->route()?->parameterNames[0] ?? null;
        // Se obtiene el ID desde la ruta
        $id = $routeName ? $this->route($routeName) : null;

        // Se valida que el ID sea númerico
        if (!is_numeric($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }

        // Se verifica si la calificación del ticket existe
        if (!CalificacionTicket::find($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "Calificación ticket no encontrada",
                404
            ));
        }

        return [
            "calificacion"=> [
                "required",
                "integer",
                "in:2,4,6,8,10"
            ],
            "observacion"=> [
                "nullable",
                // Puede contener letras, espacios, puntos, punto y coma, comas y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ\s.,;ñÑ]*$/u"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "calificacion.required"=> "La calificación del ticket es requerida",
            "calificacion.integer"=> "Solo se aceptan números enteros",
            "calificacion.in"=> "La calificación del ticket es de 2, 4, 6, 8 o 10",

            "observacion.regex"=> "Debe ser una cadena de texto"
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
