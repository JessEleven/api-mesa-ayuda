<?php

namespace App\Http\Requests\Departamento;

use App\Http\Responses\ApiResponse;
use App\Models\Departamento;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateDepartamentoRequest extends FormRequest
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
        // Se obtiene el nombre del parámetro dinámico de la ruta
        $routeName = $this->route()?->parameterNames[0] ?? null;
        // Se obtiene el ID desde la ruta
        $id = $routeName ? $this->route($routeName) : null;

        // Se valida que el ID sea númerico
        if (!is_numeric($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }

        // Se verifica si el departamento existe
        if (!Departamento::find($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "Departamento no encontrado",
                404
            ));
        }

        return [
            "nombre_departamento"=> [
                "required",
                "regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u",
                Rule::unique("departamentos")->ignore($id)
            ],
            "sigla_departamento"=> [
                "required",
                "regex:/^[A-Z][a-zA-Z]*$/",
                Rule::unique("departamentos")->ignore($id)
            ],
            "id_area"=> [
                "required",
                "exists:areas,id"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_departamento.required"=> "El nombre del departamento es requerido",
            "nombre_departamento.regex"=> "Debe ser una cadena de texto",
            "nombre_departamento.unique"=> "El nombre del departamento ya exite",

            "sigla_departamento.required"=> "La sigla del departamento es requerida",
            "sigla_departamento.regex"=> "Empieza con mayúscula y puede contener minúsculas",
            "sigla_departamento.unique"=> "La sigla del departamento ya existe",

            "id_area.required"=> "El ID área es requerido",
            "id_area.exists"=> "El ID área ingresado no existe"
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
