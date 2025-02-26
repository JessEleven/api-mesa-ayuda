<?php

namespace App\Http\Requests\PrioridadTicket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\ValidatesRegisteredTickets;
use App\Models\PrioridadTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StorePrioridadTicketRequest extends FormRequest
{
    // Reutilizando el Trait
    use ValidatesRegisteredTickets;

    public function authorize(): bool
    {
        // Uso del Trait
        // Antes de crear la prioridad se verifica si existen tickets
        $this->registeredOrActiveTickets();

        return true;
    }

    public function rules(): array
    {
        $tableName = (new PrioridadTicket())->getTable();

        return [
            "nombre_prioridad"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                "unique:" . $tableName . ",nombre_prioridad"
            ],
            "color_prioridad"=> [
                "required",
                "unique:" . $tableName . ",color_prioridad"
            ],
            "orden_prioridad"=> [
                "required",
                "integer",
                "min:1",
                "unique:" . $tableName . ",orden_prioridad"
            ],
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_prioridad.required"=> "El nombre de la prioridad es requerido",
            "nombre_prioridad.regex"=> "Debe ser una cadena de texto",
            "nombre_prioridad.unique"=> "El nombre de la prioridad debe ser única",

            "color_prioridad.required"=> "El color de la prioridad es requerido",
            "color_prioridad.unique"=> "El color de la prioridad debe ser única",

            "orden_prioridad.required"=> "La prioridad del ticket es requerida",
            "orden_prioridad.integer"=> "Solo se aceptan números enteros",
            "orden_prioridad.min"=> "Solo números enteros mayores o iguales a uno",
            "orden_prioridad.unique"=> "La prioridad del ticket debe ser única"
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
