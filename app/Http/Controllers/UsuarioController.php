<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UsuarioController extends Controller
{
    public function index()
    {
        try {
            // Relación anidada entre las tablas departamentos y areas
            $allUsers = Usuario::with(["departamentos.areas"])
                ->whereDoesntHave("tecnico_asignados")
                    ->orderBy("id", "asc")
                    ->paginate(20);

            if ($allUsers->isEmpty()) {
                return ApiResponse::success(
                    "Lista de usuarios vacia",
                    200,
                    $allUsers
                );
            }

            // Para ocultar el FK de la tabla
            $allUsers->getCollection()->transform(function ($user) {
                $user->makeHidden(["id_departamento"]);
                // Para ocultar los PKs, FKs y timestamps de las tablas relaciones
                $user->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
                $user->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);
                return $user;
            });

            return ApiResponse::success(
                "Listado de usuarios",
                200,
                $allUsers
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            $newUser = Usuario::create($request->validated());

            return ApiResponse::success(
                "Usuario creado con exito",
                201,
                $newUser->only([
                    "nombre",
                    "apellido",
                    "telefono",
                    "email",
                    "created_at"
                ])
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function show($usuario)
    {
        try {
            // Relación anidada entre las tablas departamentos y areas
            $showUser = Usuario::with(["departamentos.areas"])
                ->whereDoesntHave("tecnico_asignados")
                    ->findOrFail($usuario);

            // Para ocultar el FK de la tabla
            $showUser->makeHidden(["id_departamento"]);
            // Para ocultar los PKs, FKs y timestamps de las tablas relaciones
            $showUser->departamentos?->makeHidden(["id", "id_area", "created_at", "updated_at"]);
            $showUser->departamentos?->areas?->makeHidden(["id", "created_at", "updated_at"]);

            return ApiResponse::success(
                "Usuario encontrado con exito",
                200,
                $showUser
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Usuario no encontrado",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function update(UpdateUsuarioRequest $request, $usuario)
    {
        try {
            // Forzar un error de base de datos
            //$result = $Usuario::table('non_existent_table')->get();

            $updateUser = Usuario::findOrFail($usuario);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($updateUser->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return ApiResponse::success(
                    "No hay cambios para actualizar usuario",
                    200,
                    array_intersect_key($newData, array_flip([
                        "nombre",
                        "apellido",
                        "telefono",
                        "email"
                    ]))
                );
            }
            $updateUser->update($newData);

            return ApiResponse::success(
                "Usuario actualizado con exito",
                200,
                $updateUser->refresh()->only([
                    "nombre",
                    "apellido",
                    "telefono",
                    "email",
                    "created_at",
                    "updated_at"
                ])
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($usuario)
    {
        try {
            Usuario::findOrFail($usuario)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Usuario eliminado con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Usuario no encontrado",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }
}
