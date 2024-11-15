<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaTicketController extends Controller
{
    private $categoryRules = array (
        "nombre_categoria"=> "required|string|unique:categoria_tickets,nombre_categoria",
    );

    private $categoryMessages = array (
        "nombre_categoria.required"=> "La categoria es requerida",
        "nombre_categoria.string"=> "Debe ser una cadena de texto",
        "nombre_categoria.unique"=> "La categoria deber ser única",
    );

    private $categoryMessagesUpdate = array (
        "nombre_categoria.required"=> "La categoria es requerida",
        "nombre_categoria.string"=> "Debe ser una cadena de texto",
        "nombre_categoria.unique"=> "Ya existe una categoria igual"
    );

    public function index()
    {
        try {
            $allCategories = CategoriaTicket::all();

            if ($allCategories->isEmpty()) {
                return response()->json([
                    "message"=> "No se encontraron categorias de ticket"
                ], 200);
            }
            return response()->json([
                "message"=> "Total de categorias de ticket: ". $allCategories->count(),
                "data" => $allCategories
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->categoryRules, $this->categoryMessages);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages" => $messages],422);
            }

            $newCategoryTicket = CategoriaTicket::create([
                "nombre_categoria"=> $request->nombre_categoria
            ]);
            return response()->json([
                "success"=> "Categoria de ticket creada con exito",
                "data"=> $newCategoryTicket
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function show($categoriaTicket)
    {
        try {
            $showCategoryTicket = CategoriaTicket::findOrFail($categoriaTicket);
            return response()->json([
                "success"=> "Categoria ticket encontrada con exito",
                "data"=> $showCategoryTicket
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Categoria ticket no encontrada"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function update(Request $request, $categoriaTicket)
    {
        try {
            $categoryTicketExists = CategoriaTicket::findOrFail($categoriaTicket);

            $validator = Validator::make($request->all(), $this->categoryRules, $this->categoryMessagesUpdate);

            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json(["messages"=> $messages], 422);
            }

            $categoryTicketExists->update([
                "nombre_categoria"=> $request->nombre_categoria
            ]);

            $categoryTicketExists->refresh();
            return response()->json([
                "success"=> "Categoria ticket actualizada con exito",
                "data"=> $categoryTicketExists
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Categoria ticket no encontrada"
            ], 404);

        }   catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
            ], 500);
        }
    }

    public function destroy($categoriaTicket)
    {
        try {
            CategoriaTicket::findOrFail($categoriaTicket)->delete();
            return response()->json([
                "success"=> "Categoria ticket eliminada con exito"
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                "message"=> "Categoria ticket no encontrada"
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                "error"=> "Ha ocurrido un error inesperado"
             ], 500);
        }
    }
}
