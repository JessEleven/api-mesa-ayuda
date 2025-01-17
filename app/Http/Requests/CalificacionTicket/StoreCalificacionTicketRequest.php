<?php

namespace App\Http\Requests\CalificacionTicket;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
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
        // Usando el modelo dinámicamente para obtener el nombre de la tabla
        $tableName = (new Ticket())->getTable();

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
            ],
            "id_ticket"=> [
                "required",
                "exists:" . $tableName . ",id",
                "unique:" . $tableName . ",id"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "calificacion.required"=> "La calificación del ticket es requerida",
            "calificacion.integer"=> "Solo se aceptan números enteros",
            "calificacion.in"=> "La calificación del ticket es de 2, 4, 6, 8 o 10",

            "observacion.regex"=> "Debe ser una cadena de texto",

            "id_ticket.required"=> "El ticket es requerido",
            "id_ticket.exists"=> "El ticket ingresado no existe",
            "id_ticket.unique"=> "La calificación por ticket debe ser única"
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
