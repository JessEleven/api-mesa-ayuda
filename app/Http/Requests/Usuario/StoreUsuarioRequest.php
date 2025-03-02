<?php

namespace App\Http\Requests\Usuario;

use App\Http\Responses\ApiResponse;
use App\Models\Departamento;
use App\Models\Usuario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tableUser = (new Usuario())->getTable();
        $tableDepartment = (new Departamento())->getTable();

        return [
            "nombre"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u"
            ],
            "apellido"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u"
            ],
            "telefono"=> [
                "required",
                // Valida que el primer carácter empiece con cero
                "regex:/^0\d{9}$/"
                // No valida que el primer carácter empiece con cero
                //"regex:/^\d{10}$/"
            ],
            "email"=> [
                "required",
                "email",
                "unique:" . $tableUser . ",email"
            ],
            "password"=> [
                "required",
                "min:8",
                "regex:/^[a-zA-Z0-9ñÑ]+$/u"
            ],
            "id_departamento"=> [
                "required",
                "exists:" . $tableDepartment . ",id"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre.required"=> "Los nombres son requeridos",
            "nombre.regex"=> "Debe ser una cadena de texto",

            "apellido.required"=> "Los apellidos son requeridos",
            "apellido.regex"=> "Debe ser una cadena de texto",

            "telefono.required"=> "El teléfono es requerido",
            //"telefono.integer"=> "Debe ser un número válido",
            //"telefono.regex"=> "Debe tener al menos 10 caracteres",
            "telefono.regex"=> "Debe ser un número válido y tener 10 dígitos",

            "email.required"=> "El correo es requerido",
            "email.email"=> "La dirección del correo no es válida",
            "email.unique"=> "El correo ya está registrada",

            "password.required"=> "La contraseña es requerida",
            "password.min"=> "Debe tener al menos 8 caracteres",
            "password.regex"=> "Solo puede contener letras y números",

            "id_departamento.required"=> "El departamento es requerido",
            "id_departamento.exists"=> "El departamento ingresado no existe"
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
