<?php

namespace App\Http\Controllers;

use App\Models\CalificacionTicket;
use Illuminate\Http\Request;

class CalificacionTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $calificacion = CalificacionTicket::all();
        return response()->json([
            "calificacion_junin"=> $calificacion
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
    public function show(CalificacionTicket $calificacionTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalificacionTicket $calificacionTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalificacionTicket $calificacionTicket)
    {
        //
    }
}
