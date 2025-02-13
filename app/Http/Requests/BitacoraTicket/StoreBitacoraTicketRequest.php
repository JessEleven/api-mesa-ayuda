<?php

namespace App\Http\Requests\BitacoraTicket;

use App\Http\Responses\ApiResponse;
use App\Models\BitacoraTicket;
use App\Models\TecnicoAsignado;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreBitacoraTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tableTechnical = (new TecnicoAsignado())->getTable();
        // $tableBinnacle = (new BitacoraTicket())->getTable();

        return [
            "descripcion"=> [
                "nullable",
                // También se permiten espacios, puntos, comas, punto y coma y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ0-9\s.,;ñÑ]*$/u"
            ],
            "id_tecnico_asignado"=> [
                "required",
                function ($attribute, $value, $fail) use($tableTechnical) {
                    // Se verifica si el técnico ingresado existe o está eliminado
                    $registeredTechnician = \DB::table($tableTechnical)->find($value);

                    $technicianEliminated = \DB::table($tableTechnical)
                        ->where("id", $value)
                        ->whereNotNull("recurso_eliminado")
                        ->exists();

                    if (!$registeredTechnician || $technicianEliminated) {
                        $fail("El técnico ingresado no existe");
                    }
                },
            /*  Rule::prohibitedIf(function () use($tableBinnacle) {
                    return \DB::table($tableBinnacle)
                        ->where("id_tecnico_asignado", $this->input("id_tecnico_asignado"))
                        ->exists();
                }) */
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "descripcion.regex"=> "Debe ser una cadena de texto",

            "id_tecnico_asignado.required"=> "El técnico es requerido"
            //"id_tecnico_asignado.prohibited"=> "Ya existe una bitácora para este ticket"
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
