<?php

namespace App\Http\Requests\Usuario;

use App\Http\Responses\ApiResponse;
use App\Models\Usuario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateUsuarioRequest extends FormRequest
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
        //$id = $this->route("usuario");
        $id = Usuario::findOrFail($this->route("usuario"));

        return [
            "nombre"=> "required|regex:/^[a-zA-Z\s]+$/",
            "apellido"=> "required|regex:/^[a-zA-Z\s]+$/",
            "telefono"=> "required|integer|regex:/^\d{10}$/",
            "email"=> [
                "required",
                "email",
                 Rule::unique('usuarios')->ignore($id)
            ],
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
            "email.unique"=> "Ya existe un correo igual",

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

        $errorsCount = count($errors);

        $errorMessage = $errorsCount === 1
            ? 'Se produjo un error de validación'
            : 'Se produjeron uno o más errores de validación';

        throw new HttpResponseException(ApiResponse::validation(
            $errorMessage,
            422,
            $errors)
        );
    }
}
