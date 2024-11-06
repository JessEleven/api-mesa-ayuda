<?php

namespace App\Http\Controllers;

use App\Models\PrioridadTicket;
use Illuminate\Http\Request;

class PrioridadTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prioridadTicket = PrioridadTicket::all();
        return response()->json([
            "prioridad_junin"=> $prioridadTicket
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
    public function show(PrioridadTicket $prioridadTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrioridadTicket $prioridadTicket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrioridadTicket $prioridadTicket)
    {
        //
    }
}
