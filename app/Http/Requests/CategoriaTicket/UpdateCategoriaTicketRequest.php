<?php

namespace App\Http\Requests\CategoriaTicket;

use App\Http\Responses\ApiResponse;
use App\Models\CategoriaTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateCategoriaTicketRequest extends FormRequest
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
        $id = $this->route()?->parameterNames[0] ?? null;
        $id = $id ? $this->route($id) : null;

        if (!is_numeric($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }

        if (!CategoriaTicket::find($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "Categoria ticket no encontrada",
                404
            ));
        }

        return [
            "nombre_categoria"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                Rule::unique("categoria_tickets")->ignore($id)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_categoria.required"=> "El nombre de la categoria es requerida",
            "nombre_categoria.regex"=> "Debe ser una cadena de texto",
            "nombre_categoria.unique"=> "El nombre de la categoria ya existe"
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
