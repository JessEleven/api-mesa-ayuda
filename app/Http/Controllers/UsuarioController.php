<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{

    private $rulesUser = array(
        'nombre'=> 'required',
        'apellido'=> 'required',
        'telefono'=> 'required|',
        'email'=> 'required|',
        'password'=> 'required',
        'id_area'=> 'required',
        'id_departamento'=> 'required'
    );

    private $rulesUserUpdate = array(
        'nombre'=> 'required',
        'apellido'=> 'required',
        'telefono'=> 'required|numeric',
        'email'=> 'required',
        'password'=> 'required',
        'id_area'=> 'required',
        'id_departamento'=> 'required'
    );

    private $messagesUser = array(
        'nombre.required'=> 'El nombre es requerido',
        'apellido.required'=> 'El apellido es requerido',
        'telefono.required'=> 'El telefono es requerido',
        'email.required'=> 'El correo es requerido',
        'password.required'=> 'La contraseña es requerido',
        'id_area'=> 'El id area es requerido',
        'id_departamento'=> 'El id departamento es requerido',
    );

    public function index()
    {
        try {
            $allUsers = Usuario::with('areas', 'departamentos')->get();
            return response()->json(["Total de usuarios ". $allUsers->count(), $allUsers]);

        } catch (\Exception $e) {
            return response()->json(["error"=> $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rulesUser, $this->messagesUser);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages"=> $messages], 500);
        }

        try {
            $newUser=Usuario::create([
                "nombre" => $request->nombre,
                "apellido"=> $request->apellido,
                "telefono"=> $request->telefono,
                "email"=> $request->email,
                "password"=> $request->password,
                "id_area" => $request->id_area,
                "id_departamento"=> $request->id_departamento,
            ]);

            return response()->json([
                "message"=> "Usuario creado con exito", $newUser], 200);

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }

    public function show($usuario)
    {
        try {
            $showUser = Usuario::whereId($usuario)->with('areas', 'departamentos')->findOrFail($usuario);
            return response()->json([
                "message"=> "Usuario encontrado con exito", $showUser], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error'=> 'Usuario no encontrado'], 404);

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $usuario)
    {
        $validator = Validator::make($request->all(), $this->rulesUserUpdate, $this->messagesUser);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages"=> $messages], 500);
        }

        try {
            Usuario::whereId($usuario)->findOrFail($usuario)->update([
                "nombre"=> $request->nombre,
                "apellido"=> $request->apellido,
                "telefono"=> $request->telefono,
                "email"=> $request->email,
                "password"=> $request->password,
                "id_area" => $request->id_area,
                "id_departamento"=> $request->id_departamento,
            ]);
            return response()->json([
                "message"=> "Usuario actualizado con exito"], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error'=> 'Usuario no encontrado'], 404);
        }

        catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }

    public function destroy($usuario)
    {
        try {
            Usuario::findOrFail($usuario)->delete();
            return response()->json([
                "message"=> "Usuario eliminado con exito"], 200);

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }
}
