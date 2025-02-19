<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Usuario;
use App\Services\UsuarioModelHider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UsuarioController extends Controller
{
    public function index()
    {
        try {
            // Relación anidada entre las tablas departamentos y areas
            $allUsers = Usuario::with(["departamento.area"])
                ->whereDoesntHave("tecnicoAsignado")
                ->orderBy("id", "asc")
                ->paginate(20);

            if ($allUsers->isEmpty()) {
                return ApiResponse::index(
                    "Lista de usuarios vacía",
                    200,
                    $allUsers
                );
            }

            // Usando el servicio para ocultar los campos
            $allUsers->getCollection()->transform(function ($user) {
                return UsuarioModelHider::hideUsuarioFields($user);
            });

            return ApiResponse::index(
                "Lista de usuarios",
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

            return ApiResponse::created(
                "Usuario creado con éxito",
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
            $showUser = Usuario::with(["departamento.area"])
                ->whereDoesntHave("tecnicoAsignado")
                ->findOrFail($usuario);

            // Usando el servicio para ocultar los campos
            UsuarioModelHider::hideUsuarioFields($showUser);

            return ApiResponse::show(
                "Usuario encontrado con éxito",
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
                return ApiResponse::notUpdated(
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

            return ApiResponse::updated(
                "Usuario actualizado con éxito",
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

            $relativePath = $this->getRelativePath();
            $apiVersion = $this->getApiVersion();

            return ApiResponse::deleted(
                "Usuario eliminado con éxito",
                200,
                [
                    "related"=> $relativePath,
                    "api_version"=> $apiVersion
                ]
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
