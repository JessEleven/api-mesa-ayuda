<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreUsuarioRequest extends FormRequest
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
            "nombre"=> "required|regex:/^[a-zA-Z\s]+$/",
            "apellido"=> "required|regex:/^[a-zA-Z\s]+$/",
            "telefono"=> "required|integer|regex:/^\d{10}$/",
            "email"=> "required|email|unique:usuarios,email",
            "password"=> "required|min:8|regex:/^[a-zA-Z0-9]+$/",
            "id_area"=> "required",
            "id_departamento"=> "required"
        ];
    }

    public function messages(): array
    {
        return [
            "nombre.required"=> "Los nombres son requeridos",
            "nombre.regex"=> "Debe ser una cadena de texto",

            "apellido.required"=> "Los apellidos son requeridos",
            "apellido.regex"=> "Debe ser una cadena de texto",

            "telefono.required"=> "El telefono es requerido",
            "telefono.integer" => "Debe ser un número válido",
            "telefono.regex"=> "Debe tener al menos 10 caracteres",

            "email.required"=> "El correo es requerido",
            "email.email"=> "La dirección del correo no es válida",
            "email.unique"=> "El correo debe ser único",

            "password.required"=> "La contraseña es requerida",
            "password.min" => "Debe tener al menos 8 caracteres",
            "password.regex" => "Solo puede contener letras y números",

            "id_area"=> "El id area es requerido",
            "id_departamento"=> "El id departamento es requerido",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(response()->json([
            "messages"=> $errors,
        ], 422));
    }
}
