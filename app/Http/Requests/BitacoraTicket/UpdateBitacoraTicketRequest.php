<?php

namespace App\Http\Requests\BitacoraTicket;

use App\Http\Responses\ApiResponse;
use App\Models\BitacoraTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateBitacoraTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeName = $this->route()?->parameterNames[0] ?? null;
        $id = $routeName ? $this->route($routeName) : null;

        if (!is_numeric($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }

        $log = BitacoraTicket::find($id);
        // Se busca también las bitácoras que ha sido eliminadas
        $deletedLog = BitacoraTicket::where("id", $id)
            ->whereNotNull("recurso_eliminado")
            ->exists();

        if (!$log || $deletedLog) {
            throw new HttpResponseException(ApiResponse::error(
                "Bitácora de ticket no encontrada",
                404
            ));
        }

        return [
            "descripcion"=> [
                "nullable",
                // Puede contener letras, espacios, puntos, comas, punto y coma y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ\s.,;ñÑ]*$/u"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "descripcion.regex"=> "Debe ser una cadena de texto"
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
