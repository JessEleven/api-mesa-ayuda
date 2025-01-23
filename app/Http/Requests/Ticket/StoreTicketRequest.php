<?php

namespace App\Http\Requests\Ticket;

use App\Http\Responses\ApiResponse;
use App\Models\CategoriaTicket;
use App\Models\PrioridadTicket;
use App\Models\TecnicoAsignado;
use App\Models\Usuario;
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
        // Usando los modelos dinámicamente para obtener los nombres de las tablas
        $tableCategory = (new CategoriaTicket())->getTable();
        $tableUser = (new Usuario())->getTable();
        $tableTechnical = (new TecnicoAsignado())->getTable();
        $tablePriority = (new PrioridadTicket())->getTable();

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
            "id_categoria"=> [
                "required",
                "exists:" . $tableCategory . ",id"
            ],
             "id_usuario"=> [
                "required",
                "exists:" . $tableUser . ",id",
                Rule::prohibitedIf(function () use($tableTechnical) {
                    return \DB::table($tableTechnical)
                        ->where("id_usuario", $this->input("id_usuario"))
                        ->exists();
                })
             ],
            "id_prioridad"=> [
                "required",
                "exists:" . $tablePriority . ",id"
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

            "id_categoria.required"=> "La categoria del ticket es requerido",
            "id_categoria.exists"=> "La categoria del ticket ingresado no existe",

            "id_usuario.required"=> "El usuario es requerido",
            "id_usuario.exists"=> "El usuario ingresado no existe",
            "id_usuario.prohibited"=> "Este usuario ha sido asignado a técnico",

            "id_prioridad.required"=> "La prioridad del ticket es requerido",
            "id_prioridad.exists"=> "La prioridad del ticket ingresado no existe"
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
