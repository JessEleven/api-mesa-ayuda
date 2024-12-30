<?php

namespace App\Http\Requests\Ticket;

use App\Http\Responses\ApiResponse;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateTicketRequest extends FormRequest
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

        if (!Ticket::find($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "Ticket no encontrado",
                404
            ));
        }

        return [
            "descripcion"=> [
                "required",
                // Puede contener letras, espacios, puntos, comas, punto y coma, y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ\s.,;ñÑ]*$/u"
            ],
            "id_categoria"=> [
                "required",
                "exists:categoria_tickets,id",
            ],
             "id_usuario"=> [
                "required",
                "exists:usuarios,id",
             ],
            "id_estado"=> [
                "required",
                "exists:estado_tickets,id",
            ],
            "id_prioridad"=> [
                "required",
                "exists:prioridad_tickets,id",
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "descripcion.required"=> "La descripción del ticket es requerida",
            "descripcion.regex"=> "Debe ser una cadena de texto",

            "id_categoria.required"=> "El ID categoria ticket es requerido",
            "id_categoria.exists"=> "El ID categoria ticket ingresado no existe",

            "id_usuario.required"=> "El ID usuario es requerido",
            "id_usuario.exists"=> "El ID usuario ingresado no existe",

            "id_estado.required"=> "El ID estado ticket es requerido",
            "id_estado.exists"=> "El ID estado ticket ingresado no existe",

            "id_prioridad.required"=> "El ID prioridad ticket es requerido",
            "id_prioridad.exists"=> "El ID prioridad ticket ingresado no existe"
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
