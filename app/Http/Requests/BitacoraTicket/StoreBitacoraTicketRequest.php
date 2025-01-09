<?php

namespace App\Http\Requests\BitacoraTicket;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreBitacoraTicketRequest extends FormRequest
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
            "descripcion"=> [
                "nullable",
                // Puede contener letras, espacios, puntos, comas, punto y coma y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ\s.,;ñÑ]*$/u"
            ],
            "id_tecnico_asignado"=> [
                "required",
                "exists:tecnico_asignados,id",
                Rule::prohibitedIf(function () {
                    return \DB::table("bitacora_tickets")
                        ->where("id_tecnico_asignado", $this->input("id_tecnico_asignado"))
                            ->exists();
                })
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "descripcion.regex"=> "Debe ser una cadena de texto",

            "id_tecnico_asignado.required"=> "El técnico es requerido",
            "id_tecnico_asignado.exists"=> "El técnico ingresado no existe",
            "id_tecnico_asignado.prohibited"=> "Ya existe una bitácora para este ticket"
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
