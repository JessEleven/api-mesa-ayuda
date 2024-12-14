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
        $id = $this->route()?->parameterNames[0] ?? null;
        $id = $id ? $this->route($id) : null;

        if (!is_numeric($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }

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
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "nombre_departamento.required"=> "El nombre del departamento es requerido",
            "nombre_departamento.regex"=> "Debe ser una cadena de texto",
            "nombre_departamento.unique"=> "El nombre del departamento ya exite"
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
