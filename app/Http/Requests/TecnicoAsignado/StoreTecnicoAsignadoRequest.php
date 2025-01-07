<?php

namespace App\Http\Requests\TecnicoAsignado;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreTecnicoAsignadoRequest extends FormRequest
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
            "id_usuario"=> [
                "required",
                "exists:usuarios,id",
                function ($attribute, $value, $fail) {
                    // Un usuario que ha creado un ticket no puede ser técnico
                    $creatorUser = \DB::table("tickets")
                        ->where("id_usuario", $value)
                            ->exists();

                    if ($creatorUser) {
                        $fail("No puede ser técnico porque ha creado ticket(s)");
                    }
                }
             ],
            "id_ticket"=> [
                "required",
                "exists:tickets,id",
                function ($attribute, $value, $fail) {
                    $idUsuario = $this->input("id_usuario");

                    // El técnico no puede asignarse de nuevo el mismo ticket
                    $existingAssignment = \DB::table("tecnico_asignados")
                        ->where("id_usuario", $idUsuario)
                        ->where("id_ticket", $value)
                            ->exists();

                    if ($existingAssignment) {
                        $fail("El técnico ya tiene asignado este ticket");
                    }

                    // No se puede asignar el ticket de otro técnico
                    $ticketAssigned = \DB::table("tecnico_asignados")
                        ->where("id_ticket", $value)
                        ->where("id_usuario", "!=", $idUsuario)
                            ->exists();

                    if ($ticketAssigned) {
                        $fail("El ticket ya está asignado a otro técnico");
                    }
                }
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificar si hay errores en id_usuario
            if ($validator->errors()->has('id_usuario')) {
                // Si id_usuario no es válido, eliminar cualquier error relacionado con id_ticket
                $validator->errors()->forget('id_ticket');
            }
        });
    }

    public function messages(): array
    {
        return [
            "id_usuario.required"=> "El ID usuario es requerido",
            "id_usuario.exists"=> "El ID usuario ingresado no existe",

            "id_ticket.required"=> "El ID ticket es requerido",
            "id_ticket.exists"=> "El ID ticket ingresado no existe"
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
