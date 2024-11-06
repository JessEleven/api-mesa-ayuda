<?php

namespace App\Http\Controllers;

use App\Models\BitacoraTicket;
use Illuminate\Http\Request;

class BitacoraTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            "mensaje" => "API mesa de ayuda",
            "datos" => [
                [
                    "id" => 1,
                    "nombre" => "Bitacora 1",
                    "descripcion" => "Descripcion 1",
                ],
                [
                    "id" => 2,
                    "nombre" => "Bitacora 2",
                    "descripcion" => "Descripcion 2",
                ]
            ],
            "total" => 2,
            "status" => "success"
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
    public function show(BitacoraTicket $bitacoraTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BitacoraTicket $bitacoraTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BitacoraTicket $bitacoraTicket)
    {
        //
    }
}
