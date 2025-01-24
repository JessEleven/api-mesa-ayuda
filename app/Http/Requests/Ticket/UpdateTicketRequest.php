<?php

namespace App\Http\Requests\Ticket;

use App\Http\Responses\ApiResponse;
use App\Models\CategoriaTicket;
use App\Models\EstadoTicket;
use App\Models\PrioridadTicket;
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

        // Se verifica si el ID ingresado existe
        $ticket = Ticket::find($id);

        if (!$ticket) {
            throw new HttpResponseException(ApiResponse::error(
                "Ticket no encontrado",
                404
            ));
        }

        // Se obtiene el estado con el orden de prioridad más alto
        $maxPriority = EstadoTicket::max("orden_prioridad");

        // Si el ticket ya está en el estado con la mayor prioridad no se podra editar
        if ($ticket->id_estado && EstadoTicket::find($ticket->id_estado)->orden_prioridad == $maxPriority) {
            throw new HttpResponseException(ApiResponse::error(
                "El ticket ha sido finalizado",
                400
            ));
        }

        // Usando los modelos dinámicamente para obtener los nombres de las tablas
        $tableCategory = (new CategoriaTicket())->getTable();
        $tableStatus = (new EstadoTicket())->getTable();
        $tablePriority = (new PrioridadTicket())->getTable();

        return [
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
            $errors)
        );
    }
}
