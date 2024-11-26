<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    public function index()
    {
        try {
            $allUsers = Usuario::with("areas", "departamentos")->get();

            if ($allUsers->isEmpty()) {
                return ApiResponse::success(
                    "No se encontraron usuarios",
                    200,
                    $allUsers);
            }
            return ApiResponse::success(
                "Total de usuarios: ". $allUsers->count(),
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
                $newUser
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
            $showUser = Usuario::with("areas", "departamentos")->findOrFail($usuario);

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
                    $newData
                );
            }
            $updateUser->update($newData);

            return ApiResponse::success(
                "Usuario actualizado con exito",
                200,
                $updateUser->refresh()
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

    public function destroy($usuario)
    {
        try {
            Usuario::findOrFail($usuario)->delete();

            return ApiResponse::deleted(
                "Usuario eliminado con exito",
                200,
                ["related"=> "api/v1/usuario"]
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
