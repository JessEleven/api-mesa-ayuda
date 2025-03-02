<?php

namespace App\Http\Requests\EstadoTicket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\EstadoTicketNotFound;
use App\Models\EstadoTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateEstadoTicketRequest extends FormRequest
{
    // Reutilizando el Trait
    use EstadoTicketNotFound;

    public function authorize(): bool
    {
        // Uso del Trait
        $this->findEstadoTicketOrFail();

        return true;
    }

    public function rules(): array
    {
        // Uso del Trait
        $id = $this->findEstadoTicketOrFail();

        $tableName = (new EstadoTicket())->getTable();

        return [
            "nombre_estado"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                Rule::unique($tableName)->ignore($id)
            ],
            "color_estado"=> [
                "required",
                Rule::unique($tableName)->ignore($id)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_estado.required"=> "El nombre del estado es requerido",
            "nombre_estado.regex"=> "Debe ser una cadena de texto",
            "nombre_estado.unique"=> "El nombre del estado ya está en uso",

            "color_estado.required"=> "El color del estado es requerido",
            "color_estado.unique"=> "El color del estado ya está en uso"
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
