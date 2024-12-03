<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\CategoriaTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class CategoriaTicketController extends Controller
{
    private $categoryRules = array (
        "nombre_categoria"=> "required|regex:/^[a-zA-Z\s]+$/|unique:categoria_tickets,nombre_categoria",
    );

    private $categoryMessages = array (
        "nombre_categoria.required"=> "El nombre de la categoria es requerida",
        "nombre_categoria.regex"=> "Debe ser una cadena de texto",
        "nombre_categoria.unique"=> "El nombre de la categoria debe ser única",
    );

    private $categoryMessagesUpdate = array (
        "nombre_categoria.required"=> "El nombre de la categoria es requerida",
        "nombre_categoria.regex"=> "Debe ser una cadena de texto",
        "nombre_categoria.unique"=> "Ya existe una categoria igual"
    );

    public function index()
    {
        try {
            $allCategories = CategoriaTicket::orderBy("id", "asc")->paginate(20);

            if ($allCategories->isEmpty()) {
                return ApiResponse::success(
                    "Lista de categorias de ticket vacia",
                    200,
                    $allCategories
                );
            }
            return ApiResponse::success(
                "Listado de categorias de ticket",
                200,
                $allCategories
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
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
            return ApiResponse::success(
                "Categoria de ticket creada con exito",
                201,
                $newCategoryTicket
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500);
        }
    }

    public function show($categoriaTicket)
    {
        try {
            $showCategoryTicket = CategoriaTicket::findOrFail($categoriaTicket);

            return ApiResponse::success(
                "Categoria ticket encontrada con exito",
                200,
                $showCategoryTicket
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Categoria ticket no encontrada",
                404
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
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
            return ApiResponse::success(
                "Categoria ticket actualizada con exito",
                200,
                $categoryTicketExists
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Categoria ticket no encontrada",
                404
            );

        }   catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function destroy($categoriaTicket)
    {
        try {
            CategoriaTicket::findOrFail($categoriaTicket)->delete();

            $baseRoute = $this->getBaseRoute();

            return ApiResponse::deleted(
                "Categoria ticket eliminada con exito",
                200,
                ["related"=> $baseRoute]
            );

        } catch (ModelNotFoundException $e) {
            return ApiResponse::error(
                "Categoria ticket no encontrada",
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
