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

class UpdateTecnicoAsignadoRequest extends FormRequest
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
        $routeName = $this->route()?->parameterNames[0] ?? null;
        $id = $routeName ? $this->route($routeName) : null;

        if (!is_numeric($id)) {
            throw new HttpResponseException(ApiResponse::error(
                "El ID proporcionado no es válido",
                400
            ));
        }

        if (!TecnicoAsignado::find($id) || TecnicoAsignado::where("id", $id)
            ->whereNotNull("recurso_eliminado")->exists()) {
                throw new HttpResponseException(ApiResponse::error(
                    "Técnico asignado no encontrado",
                    404
                ));
        }

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
                "exists:" . $tableTicket . ",id",
                function ($attribute, $value, $fail) use($id, $tableTechnical) {
                    $idUsuario = $this->input("id_usuario");

                    // El técnico no puede reasignarse el mismo ticket (mismo ID de asignación se permite)
                    $existingAssignment = \DB::table($tableTechnical)
                        ->where("id_usuario", $idUsuario)
                        ->where("id_ticket", $value)
                        ->where("id", "!=", $id)
                        ->exists();

                    if ($existingAssignment) {
                        $fail("El técnico ya tiene asignado este ticket");
                    }

                    // No se puede asignar el ticket de otro técnico
                    $ticketAssigned = \DB::table($tableTechnical)
                        ->where("id_ticket", $value)
                        ->where("id_usuario", "!=", $idUsuario)
                        ->exists();

                    if ($ticketAssigned) {
                        $fail("El ticket ya existe y pertenece a otro técnico");
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
                // Si id_usuario no es válido, eliminar cualquier error relacionado con id_ticket
                $validator->errors()->forget('id_ticket');
            }
        });
    }

    public function messages(): array
    {
        return [
            "id_usuario.required"=> "El usuario es requerido",
            "id_usuario.exists"=> "El usuario ingresado no existe",

            "id_ticket.required"=> "El ticket es requerido",
            "id_ticket.exists"=> "El ticket ingresado no existe"
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
