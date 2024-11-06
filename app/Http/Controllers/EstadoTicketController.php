<?php

namespace App\Http\Controllers;

use App\Models\EstadoTicket;
use Illuminate\Http\Request;

class EstadoTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estadoTicket = EstadoTicket::all();
        return response()->json([
            "estado_junin"=> $estadoTicket
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
    public function show(EstadoTicket $estadoTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EstadoTicket $estadoTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EstadoTicket $estadoTicket)
    {
        //
    }
}
