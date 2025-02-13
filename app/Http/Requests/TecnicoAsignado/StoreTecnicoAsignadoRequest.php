<?php

namespace App\Http\Requests\TecnicoAsignado;

use App\Http\Responses\ApiResponse;
use App\Models\TecnicoAsignado;
use App\Models\Ticket;
use App\Models\Usuario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class StoreTecnicoAsignadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tableUser = (new Usuario())->getTable();
        $tableTicket = (new Ticket())->getTable();
        $tableTechnical = (new TecnicoAsignado())->getTable();

        return [
            "id_usuario"=> [
                "required",
                "exists:" . $tableUser . ",id",
                function ($attribute, $value, $fail) use($tableTicket) {
                    // Un usuario que ha creado un ticket no puede ser técnico
                    $creatorUser = \DB::table($tableTicket)
                        ->where("id_usuario", $value)
                        ->exists();

                    if ($creatorUser) {
                        $fail("No puede ser técnico porque ha creado ticket(s)");
                    }
                }
             ],
            "id_ticket"=> [
                "required",
                function($attribute, $value, $fail) use($tableTicket) {
                    // Se verifica si el ticket ingresado existe o está eliminado
                    $ticketRegistered = \DB::table($tableTicket)->find($value);

                    $ticketDeleted = \DB::table($tableTicket)
                        ->where("id", $value)
                        ->whereNotNull("recurso_eliminado")
                        ->exists();

                    if (!$ticketRegistered || $ticketDeleted) {
                        $fail("El ticket ingresado no existe");
                    }
                },
                function ($attribute, $value, $fail) use($tableTechnical) {
                    $idUsuario = $this->input("id_usuario");

                    // El técnico no puede asignarse de nuevo el mismo ticket
                    $existingAssignment = \DB::table($tableTechnical)
                        ->where("id_usuario", $idUsuario)
                        ->where("id_ticket", $value)
                        ->exists();

                    if ($existingAssignment) {
                        $fail("El técnico ya tiene asignado este ticket");
                    }

                    // No se puede asignar un ticket asigando a otro técnico
                    $ticketAssigned = \DB::table($tableTechnical)
                        ->where("id_ticket", $value)
                        ->where("id_usuario", "!=", $idUsuario)
                        ->exists();

                    if ($ticketAssigned) {
                        $fail("El ticket ya está asignado a otro técnico");
                    }
                }
            ]
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Se verifica si hay errores en el id_usuario
            if ($validator->errors()->has('id_usuario')) {
                // Si id_usuario no es válido, se elimina cualquier error relacionado con id_ticket
                $validator->errors()->forget('id_ticket');
            }
        });
    }

    public function messages(): array
    {
        return [
            "id_usuario.required"=> "El usuario es requerido",
            "id_usuario.exists"=> "El usuario ingresado no existe",

            "id_ticket.required"=> "El ticket es requerido"
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
