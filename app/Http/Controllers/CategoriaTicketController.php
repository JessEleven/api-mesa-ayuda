<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTicket;
use Illuminate\Http\Request;

class CategoriaTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            [
                "id" => 1,
                "nombre" => "Hardware"
            ],
            [
                "id" => 2,
                "nombre" => "Software"
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoriaTicket $categoriaTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoriaTicket $categoriaTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoriaTicket $categoriaTicket)
    {
        //
    }
}
