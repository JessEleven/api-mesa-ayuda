<?php

namespace App\Http\Controllers;

use App\Helpers\CodigoTicketHelper;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Http\Requests\Ticket\UpdateTicketRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Traits\HandlesNotFound\TicketNotFound;
use App\Http\Traits\HandlesServerErrors\HandleException;
use App\Http\Traits\HandlesTicketStatus;
use App\Models\EstadoTicket;
use App\Models\TecnicoAsignado;
use App\Models\Ticket;
use App\Services\TicketModelHider;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class TicketController extends Controller
{
    // Reutilizando los Traits
    use HandlesTicketStatus;
    use TicketNotFound;
    use HandleException;

    public function index()
    {
        try {
            // Relación anidada entre las tablas usuarios y departamentos
            $allTickets = Ticket::with(["usuario.departamento"])
            // Relación anidada entre las tablas usuarios, departamentos y areas
                ->with(["usuario.departamento.area"])
                ->with("categoriaTicket", "estadoTicket", "prioridadTicket")
                    ->whereNull("recurso_eliminado")
                    ->orderBy("id", "asc")
                    ->paginate(20);

            if ($allTickets->isEmpty()) {
                return ApiResponse::index(
                    "Lista de tickets vacía",
                    200,
                    $allTickets
                );
            }

            // Uso del Service (app/Services/TicketModelHider.php) para ocultar los campos
            $allTickets->getCollection()->transform(function ($ticket) {
                return TicketModelHider::hideTicketFields($ticket);
            });

            return ApiResponse::index(
                "Lista de tickets",
                200,
                $allTickets
            );

        // Uso del Trait en index, store, show, update y destroy
        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }

    public function store(StoreTicketRequest $request)
    {
        try {
            // Buscar el estado inicial para el ticket (ej. Nuevo)
            $defaultState = EstadoTicket::where("orden_prioridad", 1)
                ->first();

            if (!$defaultState) {
                return ApiResponse::error(
                    "Estado inicial para el ticket no encontrado",
                    500
                );
            }

            // Uso del Helper (app/Helpers/CodigoTicketHelper.php) para crear codigo_ticket
            $codigoTicket = CodigoTicketHelper::generateCodigoTicket(
                $request->id_usuario, $request->id_categoria
            );

            $newTicket = Ticket::create(array_merge(
                $request->validated(), [
                    "codigo_ticket"=> $codigoTicket,
                    "fecha_inicio"=> now(),
                    "id_estado"=> $defaultState->id
                ]
            ));

            return ApiResponse::created(
                "Ticket creado con éxito",
                201,
                $newTicket->only([
                    "codigo_ticket",
                    "asunto",
                    "descripcion",
                    "fecha_inicio"
                ])
            );

        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }

    public function show()
    {
        try {
            // Uso del Trait
            $showTicket = $this->findTicketOrFail();

            // Uso del Service (app/Services/TicketModelHider.php) para ocultar los campos
            TicketModelHider::hideTicketFields($showTicket);

            return ApiResponse::show(
                "Ticket encontrado con éxito",
                200,
                $showTicket
            );

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }

    public function update(UpdateTicketRequest $request, $ticket)
    {
        try {
            $updateTicket = Ticket::findOrFail($ticket);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateTicket->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::notUpdated(
                    "No hay cambios para actualizar ticket",
                    200,
                    array_intersect_key($newData, array_flip([
                        "asunto",
                        "descripcion"
                    ]))
                );
            }
            $updateTicket->update($newData);

            return ApiResponse::updated(
                "Ticket actualizado con éxito",
                200,
                $updateTicket->refresh()->only([
                    "asunto",
                    "descripcion",
                    "fecha_inicio",
                    "updated_at"
                ])
            );

        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }

    public function destroy()
    {
        try {
            // El Trait no devuelve el ID sino el modelo completo, ya que trae si el
            // ticket existe o esta eliminado (findTicketOrFail) y si esta finalizado
            // por tanto ya no es necesario traer en este caso el findTicketOrFail()
            $ticket = $this->ticketIsFinalized();

            // Se obtiene del modelo solo el ID para poder hacer la consulta
            $id = $ticket->id;

            // Ticket asignado, no se puede eliminar
            $isAssigned = TecnicoAsignado::where("id_ticket", $id)
                ->exists();

            // Se hizo por separado por temas de eficiencia
            $technicalId = TecnicoAsignado::find($id);

            if ($technicalId && $technicalId->usuario) {
                $names = explode(" ", $technicalId->usuario->nombre);
                $n = $names[0]; // Se trae el primer nombre

                $lastNames = explode(" ", $technicalId->usuario->apellido);
                $l = $lastNames[0]; // Se trae el primer apellido

                $result = $n . " " . $l;
                $code = $technicalId->ticket->codigo_ticket;

            } else {
                $code = "No se encontró el código ticket";
                $result = "No se encontró el técnico";
            }

            if ($isAssigned) {
                return ApiResponse::error(
                    "El ticket ha sido asignado",
                    400,
                    [
                        "ticket_code"=> $code,
                        "ticket_assigned_to"=> $result
                    ]
                );
            }

            // Para eliminar se usa la instancia del modelo
            $ticket->delete();

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Ticket eliminado con éxito",
                200,
                [
                    "relative_path"=> $relativePath,
                    "version"=> $apiVersion
                ]
            );

        // Trae los mensajes de los Traits (404, 400, etc.)
        } catch (HttpResponseException $e) {
            return $e->getResponse();

        } catch (Exception $e) {
            return $this->isAnException($e);
        }
    }
}
