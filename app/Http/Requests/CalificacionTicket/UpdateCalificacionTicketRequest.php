<?php

namespace App\Http\Requests\CalificacionTicket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Models\CalificacionTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateCalificacionTicketRequest extends FormRequest
{
    // Reutilizando el trait
    use HandlesRequestId;

    public function authorize(): bool
    {
        // Uso del trait
        $id = $this->validateRequestId();

        if (!CalificacionTicket::find($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "Calificación de ticket no encontrada",
                404
            ));
        }
        return true;
    }

    public function rules(): array
    {
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
                $errors
            )
        );
    }
}
