<?php

namespace App\Http\Requests\EstadoTicket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\ValidatesEstadoTicket;
use App\Models\EstadoTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreEstadoTicketRequest extends FormRequest
{
    use ValidatesEstadoTicket;

    public function authorize(): bool
    {
        // 0 porque no se pasa un ID, solo se verifica
        $this->EstadoTicketInUse(0);

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tableName = (new EstadoTicket())->getTable();

        return [
            "nombre_estado"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                "unique:" . $tableName . ",nombre_estado"
            ],
            "color_estado"=> [
                "required",
                "unique:" . $tableName . ",color_estado"
            ],
            "orden_prioridad"=> [
                "required",
                "integer",
                "min:1",
                "unique:" . $tableName . ",orden_prioridad"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_estado.required"=> "El nombre del estado es requerido",
            "nombre_estado.regex"=> "Debe ser una cadena de texto",
            "nombre_estado.unique"=> "El nombre del estado debe ser único",

            "color_estado.required"=> "El color del estado es requerido",
            "color_estado.unique"=> "El color del estado debe ser único",

            "orden_prioridad.required"=> "La prioridad del estado es requerida",
            "orden_prioridad.integer"=> "Solo se aceptan números enteros",
            "orden_prioridad.min"=> "Solo números enteros mayores o iguales a uno",
            "orden_prioridad.unique"=> "La prioridad del estado debe ser única"
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
