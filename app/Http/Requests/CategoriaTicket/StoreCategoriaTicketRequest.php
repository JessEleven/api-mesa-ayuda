<?php

namespace App\Http\Requests\CategoriaTicket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\ValidatesCategoriaTicket;
use App\Models\CategoriaTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreCategoriaTicketRequest extends FormRequest
{
    // Reutilizamos el trait
    use ValidatesCategoriaTicket;

    public function authorize(): bool
    {
        // 0 porque no se pasa un ID, solo se verifica
        $this->CategoryTicketInUse(0);

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
            "nombre_categoria.unique"=>  "El nombre de la categoria debe ser único",

            "sigla_categoria.required"=> "La sigla de la categoría es requerida",
            "sigla_categoria.regex"=>    "Solo letras mayúsculas y mínimo 2 caracteres",
            "sigla_categoria.unique"=> "La sigla de la categoría debe ser única"
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
