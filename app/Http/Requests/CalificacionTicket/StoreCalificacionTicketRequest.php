<?php

namespace App\Http\Requests\CalificacionTicket;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreCalificacionTicketRequest extends FormRequest
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
            "calificacion"=> [
                "required",
                "integer",
                "in:2,4,6,8,10"
            ],
            "observacion"=> [
                "nullable",
                // Puede contener letras, espacios, puntos, comas, punto y coma, y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ\s.,;ñÑ]*$/u"
            ],
            "id_ticket"=> [
                "required",
                "exists:tickets,id"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "calificacion.required"=> "La calificación es requerida",
            "calificacion.integer"=> "Solo se aceptan números enteros",
            "calificacion.in"=> "La calificación debe ser 2, 4, 6, 8 o 10",

            "observacion.regex"=> "Debe ser una cadena de texto",

            "id_ticket.required"=> "El ID ticket es requerido",
            "id_ticket.exists"=> "El ID ticket ingresado no existe"
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
