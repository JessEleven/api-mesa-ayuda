<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    private $rulesArea = array(
        'nombre_area'=> 'required|string|unique:areas,nombre_area',
        'peso_prioridad'=> 'required|numeric|integer|unique:areas,peso_prioridad',
    );

    private $rulesAreaUpdate = array(
        'nombre_area'=> 'required|string|unique:areas,nombre_area'
    );

    private $messgesArea = array(
        'nombre_area.required' => 'El área es requerida',
        'nombre_area.string'=> 'Debe ser una cadena de texto',
        'nombre_area.unique'=> 'El área debe ser única',
        'peso_prioridad'=> 'La prioridad es requerida',
        'peso_prioridad.numeric'=> 'Solo se aceptan números',
        'peso_prioridad.integer'=> 'No se aceptan decimales',
        'peso_prioridad.unique'=> 'La prioridad del área es única'
    );

    private $messgesAreaUpdate = array(
        'nombre_area.required' => 'El área es requerida',
        'nombre_area.string'=> 'Debe ser una cadena de texto',
        'nombre_area.unique'=> 'Ya exite una área igual',
    );

    public function index()
    {
        try {
            $allAreas = Area::all();
            return response()->json(["Total de áreas ". $allAreas->count(), $allAreas]);

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rulesArea, $this->messgesArea);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages"=> $messages], 500);
        }

        try {
            $newArea = Area::create([
                "nombre_area" => $request->nombre_area,
                "peso_prioridad"=> $request->peso_prioridad,
            ]);
            return response()->json(["success"=> "Area creada con exito", $newArea], 200);

        } catch (\Exception $e) {
            return response()->json(["error"=> $e->getMessage()], 500);
        }
    }

    public function show($area)
    {
        try {
            $showArea = Area::whereId($area)->findOrFail($area);
            return response()->json([
                "message"=> "Área encontrada con exito", $showArea], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error'=> 'Área no encontrada'], 404);

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $area)
    {
        $validator = Validator::make($request->all(), $this->rulesAreaUpdate, $this->messgesAreaUpdate);

        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json(["messages"=> $messages], 500);
        }

        try {
            Area::whereId($area)->findOrFail($area)->update([
                "nombre_area" => $request->nombre_area
                // "peso_prioridad"=> $request->peso_prioridad,
            ]);
            return response()->json([
                "message"=> "Área actualizada con exito"], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error'=> 'Área no encontrada'], 404);
        }

        catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }

    public function destroy($area)
    {
        try {
            Area::findOrFail($area)->delete();
            return response()->json(["message"=> "Área eliminada con exito"], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['error'=> 'Área no encontrada'], 404);

        } catch (\Exception $e) {
            return response()->json(['error'=> $e->getMessage()], 500);
        }
    }
}
