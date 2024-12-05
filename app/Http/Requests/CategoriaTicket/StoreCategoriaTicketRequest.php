<?php

namespace App\Http\Requests\CategoriaTicket;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreCategoriaTicketRequest extends FormRequest
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
            "nombre_categoria"=> "required|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u|unique:categoria_tickets,nombre_categoria",
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_categoria.required"=> "El nombre de la categoria es requerida",
            "nombre_categoria.regex"=> "Debe ser una cadena de texto",
            "nombre_categoria.unique"=> "El nombre de la categoria debe ser única"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        $errorsCount = count($errors);

        $errorMessage = $errorsCount === 1
            ? 'Se produjo un error de validación'
            : 'Se produjeron varios errores de validación';

        throw new HttpResponseException(ApiResponse::validation(
            $errorMessage,
            422,
            $errors)
        );
    }
}
