<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        try {
            $allUsers = Usuario::with("areas", "departamentos")->get();

            if ($allUsers->isEmpty()) {
                return response()->json([
                    "message"=> "No se encontraron usuarios"
                ], 200);
            }
            return response()->json([
                "description"=> "Total de usuarios: ". $allUsers->count(),
                "data"=> $allUsers
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function store(StoreUsuarioRequest $request)
    {
        try {
            $newUser = Usuario::create($request->validated());

            return response()->json([
                "message"=> "Usuario creado con exito",
                "data"=> $newUser
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function show($usuario)
    {
        try {
            $showUser = Usuario::with("areas", "departamentos")->findOrFail($usuario);

            return response()->json([
                "message"=> "Usuario encontrado con exito",
                "data"=> $showUser
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Usuario no encontrado"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function update(UpdateUsuarioRequest $request, $usuario)
    {
        try {
            $user = Usuario::findOrFail($usuario);

            $newData = collect($request->validated())->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            $existingData = collect($user->only(array_keys($newData)))->mapWithKeys(fn($value, $key) => [
                $key => is_string($value) ? trim($value) : $value,
            ])->toArray();

            if ($newData == $existingData) {
                return response()->json([
                    "message"=> "No hay cambios para actualizar usuario"
                ], 200);
            }

            $user->update($newData);

            return response()->json([
                "message"=> "Usuario actualizado con exito",
                "data"=> $user->refresh()
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Usuario no encontrado"
            ], 404);
        }

        catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function destroy($usuario)
    {
        try {
            Usuario::findOrFail($usuario)->delete();

            return response()->json([
                "message"=> "Usuario eliminado con exito"
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Usuario no encontrado"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }
}
