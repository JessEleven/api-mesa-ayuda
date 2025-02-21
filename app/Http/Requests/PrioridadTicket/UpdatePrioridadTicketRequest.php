<?php

namespace App\Http\Requests\PrioridadTicket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\PrioridadTicketNotFound;
use App\Http\Traits\HandlesRequestId;
use App\Models\PrioridadTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdatePrioridadTicketRequest extends FormRequest
{
    // Reutilizando los Traits
    use HandlesRequestId;
    use PrioridadTicketNotFound;

    public function authorize(): bool
    {
        // Uso de los Traits
        $id = $this->validateRequestId();

        $this->findPrioridadTicketOrFail($id);

        return true;
    }

    public function rules(): array
    {
        // Uso del Trait
        $id = $this->validateRequestId();

        $tableName = (new PrioridadTicket())->getTable();

        return [
            "nombre_prioridad"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                Rule::unique($tableName)->ignore($id)
            ],
            "color_prioridad"=> [
                "required",
                Rule::unique($tableName)->ignore($id)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_prioridad.required"=> "El nombre de la prioridad es requerida",
            "nombre_prioridad.regex"=> "Debe ser una cadena de texto",
            "nombre_prioridad.unique"=> "El nombre de la prioridad ya existe",

            "color_prioridad.required"=> "El color de la prioridad es requerida",
            "color_prioridad.unique"=> "El color de la prioridad ya existe"
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
