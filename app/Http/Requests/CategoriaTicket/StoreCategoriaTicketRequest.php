<?php

namespace App\Http\Requests\CategoriaTicket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\ValidatesRegisteredTickets;
use App\Models\CategoriaTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreCategoriaTicketRequest extends FormRequest
{
    // Reutilizando el Trait
    use ValidatesRegisteredTickets;

    public function authorize(): bool
    {
        // Uso del Trait
        // Antes de crear la categoria se verifica si existen tickets
        $this->registeredOrActiveTickets();

        return true;
    }

    public function rules(): array
    {
        $tableName = (new CategoriaTicket())->getTable();

        return [
            "nombre_categoria"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                "unique:" . $tableName . ",nombre_categoria"
            ],
            "sigla_categoria"=> [
                "required",
                "regex:/^[A-Z]{2,}$/",
                "unique:" . $tableName . ",sigla_categoria"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_categoria.required"=> "El nombre de la categoria es requerida",
            "nombre_categoria.regex"=> "Debe ser una cadena de texto",
            "nombre_categoria.unique"=>  "El nombre de la categoria ya está registrada",

            "sigla_categoria.required"=> "La sigla de la categoría es requerida",
            "sigla_categoria.regex"=>    "Solo mayúsculas y mínimo 2 caracteres",
            "sigla_categoria.unique"=> "La sigla de la categoría ya está registrada"
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
