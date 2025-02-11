<?php

namespace App\Http\Requests\Ticket;

use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesRequestId;
use App\Http\Traits\HandlesTicketStatus;
use App\Models\CategoriaTicket;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UpdateTicketRequest extends FormRequest
{
    // Reutilizando de los traits
    use HandlesRequestId;
    use HandlesTicketStatus;

    public function authorize(): bool
    {
        // Uso de los traits
        $id = $this->validateRequestId();

        $this->ticketIsFinalized($id);

        return true;
    }

    public function rules(): array
    {
        // Usando los modelos dinámicamente para obtener los nombres de las tablas
        $tableCategory = (new CategoriaTicket())->getTable();
        $tableStatus = (new EstadoTicket())->getTable();
        $tablePriority = (new PrioridadTicket())->getTable();

        return [
            "asunto"=> [
                "required",
                "regex:/^[a-zA-ZÀ-ÿ,ñÑ]+(?:\s[a-zA-ZÀ-ÿ,ñÑ]+)*$/u",
                function ($attribute, $value, $fail) {
                    // Se eliminan los espacios y se cuenta la longitud real
                    $lengthWithoutSpaces = mb_strlen(str_replace(
                        " ", "", $value
                    ));

                    if ($lengthWithoutSpaces > 25) {
                        $fail("No debe exceder los 25 caracteres");
                    }
                }
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
            "id_estado"=> [
                "required",
                "exists:" . $tableStatus . ",id"
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
            "asunto.required"=> "El asunto del ticket es requerido",
            "asunto.regex"=> "Debe ser una cadena de texto",

            "descripcion.required"=> "La descripción del ticket es requerida",
            "descripcion.regex"=> "Debe ser una cadena de texto",

            "id_categoria.required"=> "La categoria del ticket es requerido",
            "id_categoria.exists"=> "La categoria del ticket ingresado no existe",

            "id_estado.required"=> "El estado del ticket es requerido",
            "id_estado.exists"=> "El estado del ticket ingresado no existe",

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
                $errors
            )
        );
    }
}
