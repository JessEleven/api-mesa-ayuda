<?php

namespace App\Http\Requests\Ticket;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreTicketRequest extends FormRequest
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
            "codigo_ticket"=> [
                "required",
                "unique:tickets,codigo_ticket"
            ],
            "descripcion"=> [
                "required",
                // Puede contener letras, espacios, puntos, comas, punto y coma y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ\s.,;ñÑ]*$/u"
            ],
            "fecha_inicio"=> [
                "required"
            ],
            "fecha_fin"=> [
                "required"
            ],
            "id_categoria"=> [
                "required",
                "exists:categoria_tickets,id"
            ],
             "id_usuario"=> [
                "required",
                "exists:usuarios,id",
                Rule::prohibitedIf(function () {
                    return \DB::table("tecnico_asignados")
                        ->where("id_usuario", $this->input("id_usuario"))
                            ->exists();
                })
             ],
            "id_estado"=> [
                "required",
                "exists:estado_tickets,id"
            ],
            "id_prioridad"=> [
                "required",
                "exists:prioridad_tickets,id"
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "codigo_ticket.required"=> "El código del ticket es requerido",
            "codigo_ticket.unique"=> "El código del ticket debe ser único",

            "descripcion.required"=> "La descripción del ticket es requerida",
            "descripcion.regex"=> "Debe ser una cadena de texto",

            "id_categoria.required"=> "El ID categoria ticket es requerido",
            "id_categoria.exists"=> "El ID categoria ticket ingresado no existe",

            "id_usuario.required"=> "El ID usuario es requerido",
            "id_usuario.exists"=> "El ID usuario ingresado no existe",
            "id_usuario.prohibited"=> "Este usuario ha sido asignado a técnico",

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
