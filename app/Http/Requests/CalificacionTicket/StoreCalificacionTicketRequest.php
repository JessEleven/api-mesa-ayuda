<?php

namespace App\Http\Requests\CalificacionTicket;

use App\Http\Responses\ApiResponse;
use App\Models\CalificacionTicket;
use App\Models\EstadoTicket;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreCalificacionTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tableName = (new CalificacionTicket)->getTable();

        // Se obtiene el estado con el orden de prioridad más alto que existe
        $maxPriority = EstadoTicket::max("orden_prioridad");

        return [
            "calificacion"=> [
                "required",
                "integer",
                "in:2,4,6,8,10"
            ],
            "observacion"=> [
                "nullable",
                // También se permiten espacios, puntos, punto y coma, comas y la letra ñ
                "regex:/^[a-zA-ZÀ-ÿ0-9\s.,;ñÑ]*$/u"
            ],
            "id_ticket"=> [
                "required",
                Rule::unique($tableName, "id_ticket"),
                function ($attribute, $value, $fail) use ($maxPriority) {
                    // Se verifica si el ticket ingresado existe o esta eliminado
                    $ticket = Ticket::find($value);
                    $ticketDeleted = Ticket::where("id", $value)
                        ->whereNotNull("recurso_eliminado")
                        ->exists();

                    if (!$ticket || $ticketDeleted) {
                        return $fail("El ticket ingresado no existe");
                    }

                    // Se busca en que estado se encuentra el ticket actualmente
                    $currentStatus = EstadoTicket::find($ticket->id_estado);
                    // Se trae el valor en texto del estado (nombre_estado) y se convierte a mayúscula
                    $nameStade = strtolower($currentStatus->nombre_estado);

                    // Si no se encuentra el estado en su orden de prioridad máxima
                    // el ticket no ha sido finalizado, por lo tanto no se puede calificar
                    if (!$currentStatus || $currentStatus->orden_prioridad !== $maxPriority) {
                        return $fail("EL ticket actualmente tiene estado $nameStade");
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            "calificacion.required"=> "La calificación del ticket es requerida",
            "calificacion.integer"=> "Solo se aceptan números enteros",
            "calificacion.in"=> "La calificación solo acepta 2, 4, 6, 8 o 10",

            "observacion.regex"=> "Debe ser una cadena de texto",

            "id_ticket.required"=> "El ticket es requerido",
            "id_ticket.unique"=> "La calificación por ticket debe ser única"
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
